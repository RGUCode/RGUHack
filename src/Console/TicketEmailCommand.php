<?php
namespace Site\Console;

use Endroid\QrCode\QrCode;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TicketEmailCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('ticket')
            ->setDescription('Send out ticket emails')
            ->addOption('print', 'p', InputOption::VALUE_NONE, 'Used for printing output');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->container->get('db');
        $view = $this->container->get('view');
        $mail = $this->container->get('mail');

        $print = $input->getOption('print');

        $tickets = $db->table('ticket_user')
            ->where('ticket_user.emailed', false)
            ->join('user', 'user.id', '=', 'ticket_user.user_id')
            ->select('ticket_user.id', 'user.first_name', 'user.last_name', 'user.email', 'ticket_user.code')
            ->get();

        $mail->Subject = "Ticket for RGUHack";
        $mail->addReplyTo('info@rguhack.uk', 'RGUHack Team');

        $count = 0;

        foreach ($tickets as $ticket) {
            $qrcode = new QrCode($ticket->code);
            $qrcode->setSize(300);

            // Generate HTML for email
            $content = $view->fetch('email/ticket.twig', [
                'first_name' => $ticket->first_name,
                'last_name' => $ticket->last_name,
                'code' => $ticket->code,
                'qr_code' => $qrcode->getDataUri()
            ]);

            if ($print != null) {
                $output->writeln($content);
            }

            // Add to email and set address
            $full_name = $ticket->first_name + " " + $ticket->last_name;

            $mail->addAddress($ticket->email, $full_name);
            $mail->msgHTML($content);

            $sent = $mail->send();

            $db->connection()->beginTransaction();

            $db->table('ticket_user')
                ->where('id', '=', $ticket->id)
                ->update([
                    'emailed' => true
                ]);

            if ($sent) {
                $db->connection()->commit();
                $count++;
            } else {
                $db->connection()->rollBack();
            }

            $mail->clearAddresses();
        }

        $output->writeln("Tickets sent to " . $count . " participants");
    }
}

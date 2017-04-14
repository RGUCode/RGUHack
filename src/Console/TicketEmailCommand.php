<?php
namespace Site\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TicketEmailCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('ticket')
            ->setDescription('Send out ticket emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->container->get('db');
        $view = $this->container->get('view');
        $mail = $this->container->get('mail');

        $tickets = $db->table('ticket_user')
            ->where('emailed', false)
            ->join('user', 'user.id', '=', 'ticket_user.user_id')
            ->select('ticket_user.id', 'user.first_name', 'user.last_name', 'user.email', 'ticket_user.code')
            ->get();

        $mail->Subject = "Ticket for RGUHack";
        $mail->addReplyTo('info@rguhack.uk', 'RGUHack Team');

        foreach ($tickets as $ticket) {
            $db->connection()->beginTransaction();

            $db->table('ticket_user')
                ->where('id', '=', $ticket->id)
                ->update([
                    'emailed' => true
                ]);

            // Generate HTML for email
            $content = $view->fetch('email/ticket.twig', [
                'first_name' => $ticket->first_name,
                'last_name' => $ticket->last_name,
                'code' => $ticket->code
            ]);

            // Add to email and set address
            $full_name = $student->first_name + " " + $student->last_name;

            $mail->addAddress($ticket->email, $full_name);
            $mail->msgHTML($content);

            $sent = $mail->send();

            if ($sent) {
                $db->connection()->commit();
            } else {
                $db->connection()->rollBack();
            }

            $mail->clearAddresses();
        }
    }
}

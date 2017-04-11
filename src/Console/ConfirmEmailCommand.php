<?php
namespace Site\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfirmEmailCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('confirm')
            ->setDescription('Send out confirmation emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->container->get('db');
        $view = $this->container->get('view');
        $mail = $this->container->get('mail');

        // Pull out all students which have not been given confirmation
        $students = $db->table('student')
            ->whereNull('token')
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        // Setup email
        $mail->Subject = "RGUHack Confirmation";
        $mail->addReplyTo('info@rguhack.uk', 'RGUHack Team');

        // Loop through each student
        foreach ($students as $student) {
            // Get token and set it on user account
            $token = bin2hex(random_bytes(16));

            $db->connection()->beginTransaction();

            $db->table('student')
                ->where('id', $student->id)
                ->update([
                    'token' => $token
                ]);

            // Generate HTML for email
            $content = $view->fetch('email/confirm.twig', [
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'token' => $token
            ]);

            // Add to email and set address
            $full_name = $student->first_name + " " + $student->last_name;

            $mail->addAddress($student->email, $full_name);
            $mail->msgHTML($content);

            $sent = $mail->send();

            if ($sent) {
                $db->connection()->commit();
            } else {
                $db->connection()->rollBack();
            }

            $mail->clearAddresses();
        }

        $output->writeln("Sent emails to participants: " . $students->count());
    }
}

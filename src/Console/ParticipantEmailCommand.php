<?php
namespace Site\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParticipantEmailCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('participant')
            ->setDescription('Send out final email to participants')
            ->addOption('test', 't', InputOption::VALUE_NONE, 'Used for testing')
            ->addOption('print', 'p', InputOption::VALUE_NONE, 'Used for printing output');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get all of the dependencies
        $db = $this->container->get('db');
        $view = $this->container->get('view');
        $mail = $this->container->get('mail');

        // Get the options from command line
        $test = $input->getOption('test');
        $print = $input->getOption('print');

        // Pull out all users which have not been given confirmation
        $users = $db->table('user')
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        // Find all emails that we need to send to participants
        $emails = $db->table('email')
            ->where('active', true)
            ->get();

        // Setup email
        $mail->addReplyTo('info@rguhack.uk', 'RGUHack Team');

        foreach ($emails as $email) {
            $mail->Subject = $email->subject;
            $count = 0;

            // Loop through each user
            foreach ($users as $user) {
                $existing = $db->table('email_user')
                    ->where([
                        ['user_id', $user->id],
                        ['email_id', $email->id]
                    ])
                    ->exists();

                // Skip if the email has already been sent
                if ($existing) {
                    continue;
                }

                 // Generate HTML for email
                $content = $view->fetch($email->file, get_object_vars($user));

                // Print out the generated email
                if ($print != null) {
                    $output->writeln($content);
                }

                // Check if this is a test run, do not send if test
                if ($test != null) {
                    $count++;
                    continue;
                }

                $db->connection()->beginTransaction();

                $db->table('email_user')
                    ->insert([
                        'user_id' => $user->id,
                        'email_id' => $email->id,
                    ]);

                // Add to email and set address
                $full_name = $user->first_name . " " . $user->last_name;

                $mail->addAddress($user->email, $full_name);
                $mail->msgHTML($content);

                $sent = $mail->send();

                if ($sent) {
                    $db->connection()->commit();
                    $count++;
                } else {
                    $db->connection()->rollBack();
                }

                $mail->clearAddresses();
            }

            $output->writeln("Sent \"" . $email->name . "\" to participants: " . $count);
        }
    }
}


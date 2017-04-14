<?php
namespace Site\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TicketGenerateCommand extends BaseCommand
{

    protected function configure()
    {
        $this
            ->setName('ticket:generate')
            ->setDescription('Create tickets for all users')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'ID of the ticket to generate against');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->container->get('db');

        // Get the ID option
        $id = $input->getOption('id');

        if ($id == null) {
            // TODO: badness
        }

        $ticket = $db->table('ticket')
            ->where('id', '=', $id)
            ->first();

        if ($ticket != null) {
            $users = $db->table('user')
                ->get();

            foreach ($users as $user) {
                $existing = $db->table('ticket_user')
                    ->where([
                        ['user_id', '=', $user->id],
                        ['ticket_id', '=', $ticket->id],
                    ])
                    ->exists();

                if (!$existing) {
                    $db->connection()->beginTransaction();

                    $code = bin2hex(random_bytes(8));

                    $db->table('ticket_user')
                        ->insert([
                            'ticket_id' => $ticket->id,
                            'user_id' => $user->id,
                            'date' => date('Y-m-d'),
                            'code' => $code
                        ]);

                    $output->writeln("Added " . $user->first_name . " " . $user->last_name);

                    $db->connection()->commit();
                }
            }
        }
    }
}

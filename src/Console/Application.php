<?php
namespace Site\Console;

use Interop\Container\ContainerInterface;

class Application
{
    protected $container;
    protected $console;

    public function __construct($settings)
    {
        $this->container = new \Slim\Container($settings);
        $this->console = new \Symfony\Component\Console\Application('Emailer', '0.1-dev');

        $this->initialise();
    }

    private function initialise()
    {
        $this->console->add(new ConfirmEmailCommand($this->container));
        $this->console->add(new ParticipantEmailCommand($this->container));
        $this->console->add(new TicketEmailCommand($this->container));
        $this->console->add(new TicketGenerateCommand($this->container));
    }

    public function getContainer() : ContainerInterface
    {
        return $this->container;
    }

    public function run()
    {
        $this->console->run();
    }
}

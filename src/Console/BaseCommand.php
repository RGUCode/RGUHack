<?php
namespace Site\Console;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;

class BaseCommand extends Command
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->container = $container;
    }

    public function getContainer() : ContainerInterface
    {
        return $this->container;
    }
}

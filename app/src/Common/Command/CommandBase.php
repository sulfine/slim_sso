<?php

namespace App\Common\Command;

use DI\Container;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class CommandBase extends Command
{
    protected $settings;
    protected $name;
    protected $description;
    protected $help;

    public function __construct(Container $container)
    {
        parent::__construct();
        $this->settings = $container->get('settings');
    }

    protected function configure()
    {
        $attributes = new ReflectionClass(self::class);
        var_dump($attributes);
        die();
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:init-db')

            // the short description shown while running "php bin/console list"
            ->setDescription('Initialize database')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Create database structe and add initial data');;
    }

    abstract protected function execute(InputInterface $input, OutputInterface $output): int;
}

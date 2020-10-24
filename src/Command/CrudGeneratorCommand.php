<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CrudGeneratorCommand extends Command
{
    const COMMAND_VERSION = '0.14.0';
    const OPERATIONS = ['get', 'post', 'delete'];

    public function __construct($app)
    {
        parent::__construct();
        $this->container = $app->getContainer();
    }

    protected function configure()
    {
        $this->setName('api:generate:endpoints')
            ->setDescription('Given an entity, auto-generate CRUD endpoints.')
            ->setHelp('This command generate CRUD services to manage any simple entity/table, in a RESTful API. Version: ' . self::COMMAND_VERSION)
            ->addArgument(
                'entity',
                InputArgument::REQUIRED,
                'Enter the name for the entity or table, to generate endpoints.'
            )
            ->addArgument(
                'operations',
                InputArgument::IS_ARRAY,
                'Enter the operations to generate'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->container->get('db');
        $entity = $input->getArgument('entity');
        $operations = $input->getArgument('operations');

        if (!(count($operations) > 0)) {
          $operations = self::OPERATIONS;
        }
        
        $output->writeln('Generating enpoints for ' . join(', ', $operations));
        
        $generator = new CrudGeneratorService();
        $generator->generateCrud($db, $entity, $operations);
        
        $output->writeln('OK - Generated endpoints for entity: ' . $entity);
    }
}

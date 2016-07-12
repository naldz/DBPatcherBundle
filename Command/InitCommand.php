<?php

namespace Naldz\Bundle\DBPatcherBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Input\ArrayInput;


use Naldz\Bundle\DBPatcherBundle\Patch\PatchRepository;
use Naldz\Bundle\DBPatcherBundle\Patch\PatchRegistry;
use Naldz\Bundle\DBPatcherBundle\Patch\DatabasePatcher;
use Naldz\Bundle\DBPatcherBundle\Database\DatabaseCredential;

class InitCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dbpatcher:init')
            ->setDescription('Initializes the table used by the DBPatcher bundle. Be careful with this command. Never run this in prod unless you know what your are doing.')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        //initialize the database, drop and recreate
        $databasePatcher = $container->get('db_patcher.database_patcher');
        $databasePatcher->init();

        $output->writeln('Database initialized.');

        // //apply all patches
        // $command = $this->getApplication()->find('dbpatcher:apply-patch');
        // $input = new ArrayInput(array('patch-file' => null));
        // $returnCode = $command->run($input, $output);

        // if($returnCode != 0) {
        //     $output->writeln('<error>Error encountered while applying patches!</error>');
        // }

    }
}
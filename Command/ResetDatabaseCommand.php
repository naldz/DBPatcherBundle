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

class ResetDatabaseCommand extends ContainerAwareCommand
{
    
    protected function configure()
    {
        $this
            ->setName('dbpatcher:reset-database')
            ->setDescription('Resets the database into its initial state. WARNING! This is a very dangerous command. Never use this on a production server unless you know the implications. This is mostly useful in dev environments.')
            ->addArgument('init-file', InputArgument::OPTIONAL, 'The filename initial sql file in which the db_patch table should be created.')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $patchRepository = $container->get('db_patcher.patch_repository');
        $patchRegistry = $container->get('db_patcher.patch_registry');
        $databasePatcher = $container->get('db_patcher.database_patcher');

        $patchDir = $container->getParameter('db_patcher.patch_dir');

        $databasePatcher->resetDatabase($input->getArgument('init-file'));

        //apply all patches
        $command = $this->getApplication()->find('dbpatcher:apply-patch');
        $input = new ArrayInput(array('patch-file' => null));
        $returnCode = $command->run($input, $output);

        if($returnCode != 0) {
            $output->writeln('<error>Error encountered while applying patches!</error>');
        }

        $output->writeln('Database has been reset!');
    }
}
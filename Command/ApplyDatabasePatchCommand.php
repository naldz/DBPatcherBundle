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

use Naldz\Bundle\DBPatcherBundle\Patch\PatchRepository;
use Naldz\Bundle\DBPatcherBundle\Patch\PatchRegistry;
use Naldz\Bundle\DBPatcherBundle\Patch\DatabasePatcher;
use Naldz\Bundle\DBPatcherBundle\Database\DatabaseCredential;

class ApplyDatabasePatchCommand extends ContainerAwareCommand
{
    
    protected $patchRepository;
    protected $patchRegistry;
    protected $databasePatcher;
    
	protected function configure()
    {
		$this
			->setName('dbpatcher:apply-patch')
			->setDescription('Apply a database patch file')
			->addArgument('patch-file', InputArgument::OPTIONAL, 'The filename of the patch to apply if given.')
		;
    }
    
    /*** Dependency Injection ***/
    public function setPatchRepository(PatchRepository $patchRepository)
    {
        $this->patchRepository = $patchRepository;
    }

    public function setPatchRegistry(PatchRegistry $patchRegistry)
    {
        $this->patchRegistry = $patchRegistry;
    }

    public function setDatabasePatcher(DatabasePatcher $databasePatcher)
    {
        $this->databasePatcher = $databasePatcher;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        
        $patchDir = $container->getParameter('db_patcher.patch_dir');
        $dbHost = $container->getParameter('db_patcher.database_host');
        $dbUser = $container->getParameter('db_patcher.database_user');
        $dbPass = $container->getParameter('db_patcher.database_password');
        $dbName = $container->getParameter('db_patcher.database_name');
        
        $dbCred = new DatabaseCredential($dbHost, $dbUser, $dbPass, $dbName);
        
        if (is_null($this->patchRepository)) {
            $this->patchRepository = new PatchRepository($patchDir);
        }
        
        if (is_null($this->patchRegistry)) {
            $this->patchRegistry = new PatchRegistry($dbCred);
        }
        
        if (is_null($this->databasePatcher)) {
            $this->databasePatcher = new DatabasePatcher($dbCred, $patchDir);
        }
        
        $fs = new FileSystem();

        $patchesToApply = array();

        if ($input->hasArgument('patch-file') && !is_null($input->getArgument('patch-file'))) {            
            $patchFile = $input->getArgument('patch-file');
            if (!$this->patchRepository->patchFileExists($patchFile)) {
                throw new \RuntimeException(sprintf('Patch file "%s" does not exists in directory %s', $patchFile, $patchDir));
            }
            $patchesToApply = array($input->getArgument('patch-file'));
        }
        else {
            $patchesToApply = $this->patchRepository->getUnappliedPatches($this->patchRegistry);
        }

        foreach ($patchesToApply as $index => $patchFileName) {
            $output->write("Applying patch $patchFileName...");
            if ($this->databasePatcher->applyPatch($patchFileName)) {
                $output->write('registering...');
                $this->patchRegistry->registerPatch($patchFileName);
                $output->writeln('done.');
            }
            else {
                $output->writeln('ERROR!');
            }
        }

        if (count($patchesToApply)) {
            $output->writeln('Done applying patches.');
        }
        else {
            $output->writeln('No availble patch to apply.');
        }
        
	}
}
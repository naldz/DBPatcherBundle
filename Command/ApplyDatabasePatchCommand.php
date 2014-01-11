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

class ApplyDatabasePatchCommand extends ContainerAwareCommand
{
    
    protected $patchRepository;
    protected $patchRegistry;
    protected $databasePatcher;
    
	protected function configure()
    {
		$this
			->setName('dbpatcher:apply-patch')
			->setDescription('Create a patch file')
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
        
        $patchDir = $container->get('dbpatcher.patch_dir');
        $dbHost = $container->get('dbpatcher.database_host');
        $dbUser = $container->get('dbpatcher.database_user');
        $dbPass = $container->get('dbpatcher.database_password');
        $dbName = $container->get('dbpatcher.database_name');
        
        if (is_null($this->patchRepository)) {
            $this->patchRepository = new PatchRepository($patchDir);
        }
        
        if (is_null($this->patchRegistry)) {
            $this->patchRegistry = new PatchRegistry($dbHost, $dbUser, $dbPass, $dbName);
        }
        
        if (is_null($this->databasePatcher)) {
            $this->databasePatcher = new DatabasePatcher($dbHost, $dbUser, $dbPass, $dbName);
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
            if ($this->databasePatcher->applyPatch($patchFileName)) {
                $this->patchRegistry->registerPatch($patchFileName);
            }
        }
        
	}
}
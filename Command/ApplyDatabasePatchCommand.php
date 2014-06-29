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
    
	protected function configure()
    {
		$this
			->setName('dbpatcher:apply-patch')
			->setDescription('Apply a database patch file')
			->addArgument('patch-file', InputArgument::OPTIONAL, 'The filename of the patch to apply if given.')
		;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $patchRepository = $container->get('db_patcher.patch_repository');
        $patchRegistry = $container->get('db_patcher.patch_registry');
        $databasePatcher = $container->get('db_patcher.database_patcher');

        $patchDir = $container->getParameter('db_patcher.patch_dir');
        
        $fs = new FileSystem();

        $patchesToApply = array();

        if ($input->hasArgument('patch-file') && !is_null($input->getArgument('patch-file'))) {
            $patchFile = $input->getArgument('patch-file');
            if (!$patchRepository->patchFileExists($patchFile)) {
                throw new \RuntimeException(sprintf('Patch file "%s" does not exists in directory %s', $patchFile, $patchDir));
            }
            $patchesToApply = array($input->getArgument('patch-file'));
        }
        else {
            $patchesToApply = $patchRepository->getUnappliedPatches();
        }

        foreach ($patchesToApply as $index => $patchFileName) {
            $output->write("Applying patch $patchFileName...");
            if ($databasePatcher->applyPatch($patchFileName)) {
                $output->write('registering...');
                $patchRegistry->registerPatch($patchFileName);
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
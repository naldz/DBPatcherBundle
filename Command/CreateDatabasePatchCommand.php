<?php

namespace Naldz\Bundle\DBPatcherBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;


class CreateDatabasePatchCommand extends ContainerAwareCommand
{
	protected function configure()
    {
		$this
			->setName('dbpatcher:create-patch')
			->setDescription('Create a unique database patch file')
			->addArgument('filename', InputArgument::OPTIONAL, 'An optional filename to be set as the patch filename.')
		;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $patchDir = $this->getContainer()->getParameter('db_patcher.patch_dir');
        
        
        if ($input->hasArgument('filename') && !is_null($input->getArgument('filename'))) {
            $patchFileName = $input->getArgument('filename');
        }
        else {
            $patchFileName = time().'.sql';
        }
        $fs = new Filesystem();

        $fs->touch($patchDir.DIRECTORY_SEPARATOR.$patchFileName);

        $output->writeln(sprintf('Created patch file <comment>%s</comment> in %s.', $patchFileName, $patchDir));
	}
}
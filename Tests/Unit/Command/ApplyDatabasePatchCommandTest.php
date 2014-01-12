<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Unit\Command;

use Naldz\Bundle\DBPatcherBundle\Command\ApplyDatabasePatchCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;

class ApplyDatabasePatchCommandTest extends \PHPUnit_Framework_TestCase
{
    private $application;
    private $definition;
    private $kernel;
    private $container;
    private $command;
    
    private $patchRepository;
    private $patchRegistry;
    private $databasePatcher;
    
    protected function setUp()
    {

        if (!class_exists('Symfony\Component\Console\Application')) {
            $this->markTestSkipped('Symfony Console is not available.');
        }

        //mock the input definition    
        $this->definition = $this->getMockBuilder('Symfony\\Component\\Console\\Input\\InputDefinition')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->definition->expects($this->any())
            ->method('getArguments')
            ->will($this->returnValue(array()));

        $this->definition->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue(array(
                new InputOption('--verbose', '-v', InputOption::VALUE_NONE, 'Increase verbosity of messages.'),
                new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'),
                new InputOption('--no-debug', null, InputOption::VALUE_NONE, 'Switches off debug mode.'),
            )));

        //mock the kernel
        $this->kernel = $this->getMock('Symfony\\Component\\HttpKernel\\KernelInterface');
        
        //mock the helperset
        $this->helperSet = $this->getMock('Symfony\\Component\\Console\\Helper\\HelperSet');
        
        //mock the container
        $this->container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerInterface');
        $this->container->expects($this->at(0))
            ->method('getParameter')
            ->with('db_patcher.patch_dir')
            ->will($this->returnValue('/path/to/patch'));
            
        $this->container->expects($this->at(1))
            ->method('getParameter')
            ->with('db_patcher.database_host')
            ->will($this->returnValue('database_host'));
        
        $this->container->expects($this->at(2))
            ->method('getParameter')
            ->with('db_patcher.database_user')
            ->will($this->returnValue('database_user'));
            
        $this->container->expects($this->at(3))
            ->method('getParameter')
            ->with('db_patcher.database_password')
            ->will($this->returnValue('database_password'));
            
        $this->container->expects($this->at(4))
            ->method('getParameter')
            ->with('db_patcher.database_name')
            ->will($this->returnValue('database_name'));
        
        //mock the application
        $this->application = $this->getMockBuilder('Symfony\\Bundle\\FrameworkBundle\\Console\\Application')
            ->disableOriginalConstructor()
            ->getMock();
        $this->application->expects($this->any())
            ->method('getDefinition')
            ->will($this->returnValue($this->definition));
        $this->application->expects($this->any())
            ->method('getKernel')
            ->will($this->returnValue($this->kernel));
        $this->application->expects($this->once())
            ->method('getHelperSet')
            ->will($this->returnValue($this->helperSet));
        $this->kernel->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($this->container));

        $this->command = new ApplyDatabasePatchCommand();
        
        $this->patchRepository = $this->getMockBuilder('Naldz\Bundle\DBPatcherBundle\Patch\PatchRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->patchRegistry = $this->getMockBuilder('Naldz\Bundle\DBPatcherBundle\Patch\PatchRegistry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->databasePatcher = $this->getMockBuilder('Naldz\Bundle\DBPatcherBundle\Patch\DatabasePatcher')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->command->setPatchRepository($this->patchRepository);
        $this->command->setPatchRegistry($this->patchRegistry);
        $this->command->setDatabasePatcher($this->databasePatcher);

        $this->command->setApplication($this->application);
    }
    
    public function testNonExistingPatchFileThrowsException()
    {
        $this->setExpectedException('RuntimeException');
        $patchFileName = 'non-existing-patch.sql';
        $this->patchRepository
            ->expects($this->once())
            ->method('patchFileExists')
            ->with($patchFileName)
            ->will($this->returnValue(FALSE));
        
        $this->command->run(new ArrayInput(array('patch-file' => $patchFileName)), new NullOutput());
    }
    
    public function testExistingPatch()
    {
        $patchFileName = 'existing-patch.sql';
        $this->patchRepository
            ->expects($this->once())
            ->method('patchFileExists')
            ->with($patchFileName)
            ->will($this->returnValue(TRUE));
        
        $this->databasePatcher
            ->expects($this->once())
            ->method('applyPatch')
            ->with($patchFileName)
            ->will($this->returnValue(TRUE));
            
        $this->patchRegistry
            ->expects($this->once())
            ->method('registerPatch')
            ->with($patchFileName);

        $this->command->run(new ArrayInput(array('patch-file' => $patchFileName)), new NullOutput());
    }
    
    public function testApplyingAllPatches()
    {
        $unappliedPatches = array('123.sql', '456.sql');
        $this->patchRepository
            ->expects($this->once())
            ->method('getUnappliedPatches')
            ->with($this->patchRegistry)
            ->will($this->returnValue($unappliedPatches));
            
        $this->databasePatcher
            ->expects($this->at(0))
            ->method('applyPatch')
            ->with($unappliedPatches[0])
            ->will($this->returnValue(TRUE));
            
        $this->databasePatcher
            ->expects($this->at(1))
            ->method('applyPatch')
            ->with($unappliedPatches[1])
            ->will($this->returnValue(TRUE));
            
        $this->patchRegistry
            ->expects($this->at(0))
            ->method('registerPatch')
            ->with($unappliedPatches[0]);
            
        $this->patchRegistry
            ->expects($this->at(1))
            ->method('registerPatch')
            ->with($unappliedPatches[1]);

        $this->command->run(new ArrayInput(array()), new NullOutput());
    }
    
    public function testDefectivePatchShouldThrowException()
    {
        $this->setExpectedException('RuntimeException');
        $unappliedPatches = array('123.sql', '456.sql', '789.sql');
        $this->patchRepository
            ->expects($this->once())
            ->method('getUnappliedPatches')
            ->with($this->patchRegistry)
            ->will($this->returnValue($unappliedPatches));
            
        $this->databasePatcher
            ->expects($this->at(0))
            ->method('applyPatch')
            ->with($unappliedPatches[0])
            ->will($this->returnValue(TRUE));
            
        $this->databasePatcher
            ->expects($this->at(1))
            ->method('applyPatch')
            ->with($unappliedPatches[1])
            ->will($this->throwException(new \RuntimeException() ));
            
        $this->patchRegistry
            ->expects($this->once())
            ->method('registerPatch')
            ->with($unappliedPatches[0]);

        $this->command->run(new ArrayInput(array()), new NullOutput());
    }
    
}

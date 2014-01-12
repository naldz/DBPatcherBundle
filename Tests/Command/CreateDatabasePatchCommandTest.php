<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Command;

use Naldz\Bundle\DBPatcherBundle\Command\CreateDatabasePatchCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;

class CreateDatabasePatchCommandTest extends \PHPUnit_Framework_TestCase
{
    private $patchDir;
    private $application;
    private $definition;
    private $kernel;
    private $container;
    private $command;
    
    protected function setUp()
    {

        if (!class_exists('Symfony\Component\Console\Application')) {
            $this->markTestSkipped('Symfony Console is not available.');
        }
        
        $this->patchDir = sys_get_temp_dir().'/db_patches';
        //create the patchDir directory
        if (!is_dir($this->patchDir)) {
            mkdir($this->patchDir);
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
        $this->container->expects($this->once())
            ->method('get')
            ->with('db_patcher.patch_dir')
            ->will($this->returnValue($this->patchDir));
        
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

        $this->command = new CreateDatabasePatchCommand();
        $this->command->setApplication($this->application);
    }
    
    protected function tearDown()
    {
        if (is_dir($this->patchDir)) {
            array_map('unlink', glob($this->patchDir.'/*'));
            rmdir($this->patchDir);
        }
    }
    
    public function testCreatePatchFile()
    {
        $patchFileName = '123456789.sql';
        $this->command->run(new ArrayInput(array('filename' => $patchFileName)), new NullOutput());

        $this->assertFileExists($this->patchDir.DIRECTORY_SEPARATOR.$patchFileName);
    }
    
    public function testCreatePatchFileNoArgument()
    {
        $consoleOutput = '';
        $output = $this->getMock('Symfony\\Component\\Console\\Output\\OutputInterface');
        $output->expects($this->any())
            ->method('writeln')
            ->will($this->returnCallback(function ($messages, $type) use (&$consoleOutput) {
                $consoleOutput = $messages;
            }));

        $this->command->run(new ArrayInput(array()), $output);
        
        $startsAt = strpos($consoleOutput, "<comment>") + strlen("<comment>");
        $endsAt = strpos($consoleOutput, "</comment>", $startsAt);
        $patchFileName = substr($consoleOutput, $startsAt, $endsAt - $startsAt);

        $this->assertFileExists($this->patchDir.DIRECTORY_SEPARATOR.$patchFileName);
    }
    
}

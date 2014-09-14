<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Naldz\Bundle\DBPatcherBundle\TestHelper\App\AppKernel;
use Naldz\Bundle\DBPatcherBundle\TestHelper\CommandExecutor\CommandExecutor;

abstract class CommandTestCase extends \PHPUnit_Framework_TestCase
{

    protected $appRoot;
    protected $kernel;
    protected $application;
    protected $commandExecutor;
    protected $env;

    public function setUp()
    {
        $this->appRoot = __DIR__.'/../../../TestHelper/App';
        $this->kernel = new AppKernel($this->env, true);
        $this->application = new Application($this->kernel);
        $this->commandExecutor = new CommandExecutor($this->application);

        //boot the kernel
        $this->kernel->boot();
    }

}
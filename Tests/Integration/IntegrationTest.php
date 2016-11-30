<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Naldz\Bundle\DBPatcherBundle\TestHelper\App\AppKernel;
use Symfony\Component\Console\Input\ArgvInput;


class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected $appRoot;
    protected $kernel;

    public function setUp()
    {
        $this->kernel = new AppKernel('mysql', true);
        $this->appRoot = __DIR__.'/../Helper/App';
        //boot the kernel
        $this->kernel->boot();
        // $application = new Application($this->kernel);
        // $application->run(new ArgvInput(array('bin/console', 'debug:container', '--parameters')));

        //remove the cache files from the app
        $this->fs = new FileSystem();
        $this->fs->remove(array($this->appRoot.'/cache', $this->appRoot.'/logs'));
    }

    public function testServicesAreDefined()
    {
        $container = $this->kernel->getContainer();
        $this->assertNotNull($container->get('db_patcher.mysql_dsn_parser'));
        $this->assertNotNull($container->get('db_patcher.sqlite_dsn_parser'));
        $this->assertNotNull($container->get('db_patcher.mysql_patcher_driver'));
        $this->assertNotNull($container->get('db_patcher.sqlite_patcher_driver'));
        $this->assertNotNull($container->get('db_patcher.driver_configurator'));
        $this->assertNotNull($container->get('db_patcher.patch_registry'));
        $this->assertNotNull($container->get('db_patcher.patch_repository'));
        $this->assertNotNull($container->get('db_patcher.database_patcher'));
    }
}
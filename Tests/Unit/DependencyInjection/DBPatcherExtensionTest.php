<?php

namespace Naldz\Bundle\DBPatcher\Tests\Unit\DependencyInjection;

use Naldz\Bundle\DBPatcherBundle\DependencyInjection\DBPatcherExtension;
use Naldz\Bundle\DBPatcherBundle\DependencyInjection\Compiler\ConfigurationFilterPass;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpFoundation\Request;

class DBPatcherExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $kernel;
    private $container;
    
    protected function setUp()
    {
        $this->kernel = $this->getMock('Symfony\\Component\\HttpKernel\\KernelInterface');
        $this->container = new ContainerBuilder();
    }

    public function testInvalidPatchDirOptionShouldThrowException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->container->addCompilerPass(new ConfigurationFilterPass());
        $extension = new DBPatcherExtension();
        $options = $this->composeOptions();
        $options['patch_dir'] = '/nowhere/path';
        $config = array('db_patcher' => $options);
        $extension->load($config, $this->container);
        $this->container->compile();
    }

    /**
     * @dataProvider missingOptionDataProvider
     */
    public function testMissingOptionShouldThrowException($option)
    {
        $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');
        $this->container->addCompilerPass(new ConfigurationFilterPass());
        $extension = new DBPatcherExtension();
        $config = array($this->composeOptions(array($option)));
        $extension->load($config, $this->container);
        $this->container->compile();
    }
    
    public function missingOptionDataProvider()
    {
        return array(
            array('patch_dir'),
            array('database_host'),
            array('database_user'),
            array('database_password'),
            array('database_name')
        );
    }
    
    private function composeOptions($lessOptions=array())
    {
        $completeOptions = array(
            'patch_dir' => sys_get_temp_dir(),
            'database_host' => 'database_host_option',
            'database_user' => 'database_user_option',
            'database_password' => 'database_password_option',
            'database_name' => 'database_name_option'
        );
        
        return array_diff_key($completeOptions, array_flip($lessOptions));
    }
}

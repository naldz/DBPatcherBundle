<?php

namespace Naldz\Bundle\DBPatcherBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class DBPatcherExtension extends Extension
{
    /**
     * Loads the configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        //load the service configuration
        $processor = new Processor();
        $configuration = $this->getConfiguration($configs, $container);
        $config = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('db_patcher.patch_dir', $config['patch_dir']);
        $container->setParameter('db_patcher.dsn', $config['dsn']);
        $container->setParameter('db_patcher.driver_client_path', $config['driver_client_path']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

    }
}
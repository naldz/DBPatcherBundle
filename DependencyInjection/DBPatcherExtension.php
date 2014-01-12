<?php

namespace Naldz\Bundle\DBPatcherBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
        $processor = new Processor();
                
        $configuration = $this->getConfiguration($configs, $container);
        $config = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('dbpatcher.patch_dir', $config['patch_dir']);
        $container->setParameter('dbpatcher.database_host', $config['database_host']);
        $container->setParameter('dbpatcher.database_user', $config['database_host']);
        $container->setParameter('dbpatcher.database_password', $config['database_host']);
        $container->setParameter('dbpatcher.database_name', $config['database_host']);

    }
}

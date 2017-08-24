<?php

// src/Acme/HelloBundle/DependencyInjection/Configuration.php
namespace Naldz\Bundle\DBPatcherBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('db_patcher');

        $rootNode
            ->children()
                ->scalarNode('patch_dir')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('dsn')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('driver_client_path')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->integerNode('timeout')
                    ->defaultValue(null)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
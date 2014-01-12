<?php

namespace Naldz\Bundle\DBPatcherBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;

class ConfigurationFilterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $patchDir = $container->getParameterBag()->resolveValue($container->getParameter('db_patcher.patch_dir'));
        $fs = new Filesystem();
        if (!$fs->exists($patchDir)) {
            throw new \InvalidArgumentException(sprintf('The "db_patcher.patch_dir" (%s) directory does not exist.', $patchDir));
        }
    }
}

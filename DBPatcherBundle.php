<?php

namespace Naldz\Bundle\DBPatcherBundle;


use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Naldz\Bundle\DBPatcherBundle\DependencyInjection\Compiler\ConfigurationFilterPass;

class DBPatcherBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigurationFilterPass());
    }
}
<?php

namespace Naldz\Bundle\DBPatcherBundle\Patcher;

use Naldz\Bundle\DBPatcherBundle\Patcher\Driver\PatcherDriverInterface;

interface DrivableInterface
{
    
    public function setDriver(PatcherDriverInterface $driver);

}
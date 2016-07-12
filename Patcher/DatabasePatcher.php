<?php

namespace Naldz\Bundle\DBPatcherBundle\Patcher;

use Naldz\Bundle\DBPatcherBundle\Patcher\DrivableInterface;
use Naldz\Bundle\DBPatcherBundle\Patcher\Driver\PatcherDriverInterface;

use Symfony\Component\Process\Process;

class DatabasePatcher implements DrivableInterface
{
    private $patchDir;
    private $patcherDriver;

    public function __construct($patchDir)
    {
        $this->patchDir = $patchDir;
    }

    public function setDriver(PatcherDriverInterface $patcherDriver)
    {
        $this->patcherDriver = $patcherDriver;
    }
    
    public function applyPatch($patchFile, $process=null)
    {
        $fullPatchFile = $this->patchDir.DIRECTORY_SEPARATOR.$patchFile;
        $this->patcherDriver->applyPatch($fullPatchFile);

        return true;
    }

    // public function resetDatabase($initFile = null)
    // {
    //     $this->patcherDriver->resetDatabase($initFile);
    // }

    public function init()
    {
        $this->patcherDriver->init();
    }

}
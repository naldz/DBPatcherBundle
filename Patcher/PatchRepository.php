<?php

namespace Naldz\Bundle\DBPatcherBundle\Patcher;

use Naldz\Bundle\DBPatcherBundle\Patcher\PatchRegistry;
use Naldz\Bundle\DBPatcherBundle\Patcher\DrivableInterface;
use Naldz\Bundle\DBPatcherBundle\Patcher\Driver\PatcherDriverInterface;

use Symfony\Component\Finder\Finder;

class PatchRepository implements DrivableInterface
{
    protected $patchDir;
    protected $patchRegistry;
    protected $patcherDriver;

    public function __construct($patchDir, PatchRegistry $patchRegistry)
    {
        $this->patchDir = $patchDir;
        $this->patchRegistry = $patchRegistry;
    }
    
    public function setDriver(PatcherDriverInterface $driver)
    {
        $this->patcherDriver = $patcherDriver;
    }

    public function getUnappliedPatches(Finder $finder=null)
    {
        $registeredPatches = $this->patchRegistry->getRegisteredPatches();

        if (is_null($finder)) {
            $finder = $finder = new Finder();
        }
        $finder->files()->in($this->patchDir)->sortByName();
        
        $unappliedPatches = array();

        foreach ($finder as $file) {
            $patchFileName = $file->getFilename();

            if (!array_key_exists($patchFileName, $registeredPatches)) {
                $unappliedPatches[] = $patchFileName;
            }
        }

        return $unappliedPatches;
    }
    
    public function patchFileExists($patchFileName, Finder $finder=null)
    {
        if (is_null($finder)) {
            $finder = $finder = new Finder();
        }
        $finder->files()->in($this->patchDir)->name($patchFileName);
        
        return $finder->count() > 0;
    }
}
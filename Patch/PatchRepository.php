<?php

namespace Naldz\Bundle\DBPatcherBundle\Patch;

use Naldz\Bundle\DBPatcherBundle\Patch\PatchRegistry;
use Symfony\Component\Finder\Finder;

class PatchRepository
{
    protected $patchDir;
    
    public function __construct($patchDir)
    {
        $this->patchDir = $patchDir;
    }
    
    public function getUnappliedPatches(PatchRegistry $patchRegistry, $finder=null)
    {
        $registeredPatches = $patchRegistry->getRegisteredPatches();
        
        //retrieve all the files
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
}
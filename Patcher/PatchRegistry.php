<?php

namespace Naldz\Bundle\DBPatcherBundle\Patcher;

use Naldz\Bundle\DBPatcherBundle\Patcher\DrivableInterface;
use Naldz\Bundle\DBPatcherBundle\Patcher\Driver\PatcherDriverInterface;

class PatchRegistry implements DrivableInterface
{
    private $patcherDriver;
    
    public function setDriver(PatcherDriverInterface $patcherDriver)
    {
        $this->patcherDriver = $patcherDriver;
    }

    public function getRegisteredPatches()
    {
        //get all records from the database and return an array
        $con = $this->patcherDriver->getConnection();

        $stmt = $con->prepare("SELECT * FROM db_patch;");
        $stmt->execute();
        
        $appliedPatches = $stmt->fetchAll();

        $appliedPatchesName = array();
        foreach ($appliedPatches as $iAppliedPatch) {
            $appliedPatchesName[$iAppliedPatch['name']] = $iAppliedPatch['date_applied'];
        }
        
        $stmt->closeCursor();
        
        return $appliedPatchesName;
    }
    
    public function registerPatch($patchName)
    {
        $con = $this->patcherDriver->getConnection();
        $stmt = $con->prepare("INSERT INTO db_patch (name) VALUES (:name)");
        $stmt->execute(array(':name' => $patchName));
    }
}
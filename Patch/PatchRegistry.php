<?php

namespace Naldz\Bundle\DBPatcherBundle\Patch;

use Naldz\Bundle\DBPatcherBundle\Database\DatabaseCredential;

class PatchRegistry
{
    
    private $dbCred;
    private $connection;
    private $pdoClass;
    
    public function __construct(DatabaseCredential $dbCred)
    {
        $this->dbCred = $dbCred;
        $this->pdoClass = '\PDO';
    }

    public function setPdoClass($pdoClass)
    {
        $this->pdoClass = $pdoClass;
    }

    public function getConnection()
    {
        if (is_null($this->connection)) {
            $connString = sprintf('mysql:host=%s;dbname=%s', $this->dbCred->getHost(), $this->dbCred->getDatabaseName());
            $this->connection = new $this->pdoClass($connString, $this->dbCred->getUser(), $this->dbCred->getPassword());
        }
        return $this->connection;
    }
    
    public function getRegisteredPatches($con = null)
    {
        //get all records from the database and return an array
        if (is_null($con)) {
            $con = $this->getConnection();
        }

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
    
    public function registerPatch($patchName, $con = null)
    {
        if (is_null($con)) {
            $con = $this->getConnection();
        }
        
        $stmt = $con->prepare("INSERT INTO db_patch (name) VALUES (:name)");
        $stmt->execute(array(':name' => $patchName));
        
        //insert this new patch
    }
}
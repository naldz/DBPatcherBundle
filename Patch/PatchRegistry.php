<?php

namespace Naldz\Bundle\DBPatcherBundle\Patch;

class PatchRegistry
{
    private $dhHost;
    private $dbUser;
    private $dbPass;
    private $dbName;

    private $connection;
    private $pdoClass;
    
    public function __construct($dbHost, $dbUser, $dbPass, $dbName)
    {
        $this->dbHost = $dbHost;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
        $this->dbName = $dbName;
        
        $this->pdoClass = '\PDO';
    }

    public function setPdoClass($pdoClass)
    {
        $this->pdoClass = $pdoClass;
    }

    public function getConnection()
    {
        if (is_null($this->connection)) {
            $connString = sprintf('mysql:host=%s;dbname=%s', $this->dbHost, $this->dbName);
            $this->connection = new $this->pdoClass($connString, $this->dbUser, $this->dbPass);
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
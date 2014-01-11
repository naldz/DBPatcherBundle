<?php

namespace Naldz\Bundle\DBPatcherBundle\Patch;

use Naldz\Bundle\DBPatcherBundle\Patch\PatchRegistry;
use Naldz\Bundle\DBPatcherBundle\Database\DatabaseCredential;

use Symfony\Component\Process\Process;

class DatabasePatcher
{
    private $patchRegistry;
    private $patchDir;
    private $mysqlBin;
    private $dbCred;
    
    public function __construct(PatchRegistry $patchRegistry, DatabaseCredential $dbCred, $patchDir, $mysqlBin = '/usr/bin/mysql' )
    {
        $this->patchRegistry = $patchRegistry;
        $this->dbCred = $dbCred;
        $this->patchDir = $patchDir;
        $this->mysqlBin = $mysqlBin;
    }
    
    public function applyPatch($patchFile, $process=null)
    {
         $fullPatchFile = $this->patchDir.DIRECTORY_SEPARATOR.$patchFile;
         
         //$applyPatchProc = new Process("mysql -h$dbHost -u$dbUser -p$dbPass $dbName < $sqlFilePathName");
         $cmdString = sprintf("%s -h%s -u%s -p%s %s < %s", 
             $this->mysqlBin, 
             $this->dbCred->getHost(),
             $this->dbCred->getUser(),
             $this->dbCred->getPassword(),
             $this->dbCred->getDatabaseName(),
             $fullPatchFile
         );
         
         if (is_null($process)) {
             $process = new Process($cmdString);
         }
         
         $process->run();
         
         if (!$process->isSuccessful()) {
             throw new \RuntimeException($process->getErrorOutput());
         }
         
         $this->patchRegistry->registerPatch($patchFile);
    }
}
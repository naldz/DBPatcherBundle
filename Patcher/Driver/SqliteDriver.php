<?php

namespace Naldz\Bundle\DBPatcherBundle\Patcher\Driver;

use Naldz\Bundle\DBPatcherBundle\Patcher\Driver\PatcherDriverInterface;
use Naldz\Bundle\DBPatcherBundle\Patcher\PatchRegistry;

use Symfony\Component\Process\Process;

class SqliteDriver implements PatcherDriverInterface
{
    private $clientBin;
    private $dsn;
    private $dsnParser;
    private $timeout;

    private $connection;
    private $creds;

    public function __construct($dsnParser, $dsn, $clientBin = '/usr/bin/sqlite3', $timeout)
    {
        $this->clientBin = $clientBin;
        $this->dsn = $dsn;
        $this->dsnParser = $dsnParser;
        $this->timeout = $timeout;
    }

    public function getConnection($pdoClass = '\PDO')
    {
        if (is_null($this->connection)) {
            $creds = $this->getParsedCreds();

            $connString = sprintf('sqlite:%s', $creds['database_file']);

            $this->connection = new $pdoClass($connString);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return $this->connection;
    }

    public function applyPatch($fullPatchFile, $process=null)
    {
        $creds = $this->getParsedCreds();

        $cmdString = sprintf("cat %s | %s %s",
            $fullPatchFile,
            $this->clientBin,
            $creds['database_file']
        );
        
        if (is_null($process)) {
            $process = new Process($cmdString);
            $process->setTimeout($this->timeout);
        }
         
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return true;
    }

    // public function resetDatabase($initFile = null, $rmProc = null, $createProc = null, $initProc = null)
    // {

    //     $creds = $this->getParsedCreds();

    //     //remove the current database
    //     if (is_null($rmProc)) {
    //         $rmProc = new Process(sprintf('rm -f %s', $creds['database_file']));
    //     }
        
    //     $rmProc->run();
        
    //     if (!$rmProc->isSuccessful()) {
    //         throw new \RuntimeException($rmProc->getErrorOutput());
    //     }

    //     //recreate the database file
    //     $patchTableSql = 'CREATE TABLE "db_patch" ("id" INTEGER PRIMARY KEY AUTOINCREMENT, "name" varchar(50) NOT NULL, "date_applied" DEFAULT CURRENT_TIMESTAMP);';

    //     if (is_null($createProc)) {
    //         $createProc = new Process(sprintf("%s %s '%s'", $this->clientBin, $creds['database_file'], $patchTableSql));
    //     }

    //     $createProc->run();

    //     if (!$createProc->isSuccessful()) {
    //         throw new \RuntimeException($createProc->getErrorOutput());
    //     }

    //     //initialize the database file
    //     if (!is_null($initFile)) {
    //         if (is_null($initProc)) {
    //             $initProc = new Process(sprintf('%s %s < %s', $this->clientBin, $creds['database_file'], $initFile));
    //         }
            
    //         $initProc->run();
    //         if (!$initProc->isSuccessful()) {
    //             throw new \RuntimeException($initProc->getErrorOutput());
    //         }
    //     }

    // }

    protected function getParsedCreds()
    {
        if (is_null($this->creds)) {
            $this->creds = $this->dsnParser->parse($this->dsn);
        }

        return $this->creds;
    }

    public function init($initProc=null)
    {
        $creds = $this->getParsedCreds();

        //recreate the database file
        $patchTableSql = 'CREATE TABLE IF NOT EXISTS "db_patch" ("id" INTEGER PRIMARY KEY AUTOINCREMENT, "name" varchar(50) NOT NULL, "date_applied" DEFAULT CURRENT_TIMESTAMP);';

        if (is_null($initProc)) {
            $initProc = new Process(sprintf("%s %s '%s'", $this->clientBin, $creds['database_file'], $patchTableSql));
        }

        $initProc->run();

        if (!$initProc->isSuccessful()) {
            throw new \RuntimeException($initProc->getErrorOutput());
        }

    }

}
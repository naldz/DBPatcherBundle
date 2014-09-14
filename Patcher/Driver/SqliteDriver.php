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

    private $connection;
    private $creds;

    public function __construct($dsnParser, $dsn, $clientBin = '/usr/bin/sqlite3')
    {
        $this->clientBin = $clientBin;
        $this->dsn = $dsn;
        $this->dsnParser = $dsnParser;
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
         }
         
         $process->run();
         
         if (!$process->isSuccessful()) {
             throw new \RuntimeException($process->getErrorOutput());
         }

         return true;
    }

    protected function getParsedCreds()
    {
        if (is_null($this->creds)) {
            $this->creds = $this->dsnParser->parse($this->dsn);
        }

        return $this->creds;
    }

}
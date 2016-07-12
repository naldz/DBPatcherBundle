<?php

namespace Naldz\Bundle\DBPatcherBundle\Patcher\Driver;

use Naldz\Bundle\DBPatcherBundle\Patcher\Driver\PatcherDriverInterface;
use Naldz\Bundle\DBPatcherBundle\Patcher\PatchRegistry;

use Symfony\Component\Process\Process;

class MysqlDriver implements PatcherDriverInterface
{
    private $clientBin;
    private $dsn;
    private $dsnParser;

    private $connection;
    private $creds;

    public function __construct($dsnParser, $dsn, $clientBin = '/usr/bin/mysql')
    {
        $this->clientBin = $clientBin;
        $this->dsn = $dsn;
        $this->dsnParser = $dsnParser;
    }

    public function getConnection($pdoClass = '\PDO')
    {
        if (is_null($this->connection)) {
            $creds = $this->getParsedCreds();

            $connString = sprintf('mysql:host=%s;dbname=%s', $creds['host'], $creds['database']);

            $this->connection = new $pdoClass($connString, $creds['user'], $creds['password']);
        }

        return $this->connection;
    }

    public function applyPatch($fullPatchFile, $process=null)
    {

        $creds = $this->getParsedCreds();

        $cmdString = sprintf("%s -h%s -u%s -p%s %s < %s", 
            $this->clientBin, 
            $creds['host'],
            $creds['user'],
            $creds['password'],
            $creds['database'],
            $fullPatchFile
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

    public function init($initProc=null)
    {
        $creds = $this->getParsedCreds();
        //create the table
        $initSql = "CREATE TABLE IF NOT EXISTS `db_patch` (`id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT, `name` varchar(50) NOT NULL, `date_applied` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8; TRUNCATE TABLE db_patch;";

        // $cmdString = sprintf(
        //     '%s -h%s -u%s -p%s --database=%s -e "%s"',
        //     $this->clientBin,
        //     $creds['host'],
        //     $creds['user'],
        //     $creds['password'],
        //     $creds['database'],
        //     $initSql
        // );
        // if (is_null($initProc)) {
        //     //mysql -hlocalhost -uroot -ppassword -e "SHOW DATABASES";
        //     $initProc = new Process($cmdString);
        // }
        // $initProc->run();

        // if (!$initProc->isSuccessful()) {
        //     throw new \RuntimeException($initProc->getErrorOutput());
        // }

        $con = $this->getConnection();
        $con->exec($initSql);
    }

}
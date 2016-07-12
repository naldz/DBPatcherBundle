<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command;

use Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command\CommandTestCase;
use Symfony\Component\Filesystem\Filesystem;

class InitCommandSqliteTest extends CommandTestCase
{
    protected $env = 'sqlite';
    protected $tempDbFile;
    protected $dbh = null;

    public function setUp()
    {
        parent::setUp();
        //remove database file
        $this->tempDbFile = $this->appRoot.'/database/testdb.sqlite';
        $fs = new Filesystem();
        if ($fs->exists($this->tempDbFile)) {
            $fs->remove($this->tempDbFile);
        }

        $dbFile = $this->appRoot.'/../Fixture/sqlite/testdb.sqlite';
        $fs->copy($dbFile, $this->tempDbFile);
    }

    protected function getConnection()
    {
        //if (is_null($this->dbh)) {
            $dsn = 'sqlite:'.$this->tempDbFile;
            $this->dbh = new \PDO($dsn);
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        //}

        return $this->dbh;
    }

    protected function dropDbPatchTable()
    {
        $pdoCon = $this->getConnection();
        $stmt = $pdoCon->exec("DROP TABLE db_patch;");
    }

    public function testInitDatabaseShouldCreateTheDBPatcherTable()
    {
        $this->dropDbPatchTable();
        $this->commandExecutor->execute(array(
            'command' => 'dbpatcher:init'
        ));

        // //check if the table has been created
        $pdoCon = $this->getConnection();
        $stmt = $pdoCon->query('SELECT name FROM sqlite_master WHERE type="table" AND name="db_patch";');
        $this->assertEquals(1, count($stmt->fetchAll()));
    }

}
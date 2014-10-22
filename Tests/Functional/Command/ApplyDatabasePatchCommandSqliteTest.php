<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command;

use Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command\ApplyDatabasePatchCommandTestCase;
use Symfony\Component\Filesystem\Filesystem;

class ApplyDatabasePatchCommandSqliteTest extends ApplyDatabasePatchCommandTestCase
{

    protected $env = 'sqlite';
    protected $tempDbFile;
    protected $dbSetUp = false;
    protected $dbh = null;

    public function setUp()
    {
        $this->dbSetUp = false;
        parent::setUp();
    }

    protected function setUpTempDatabase()
    {
        if (!$this->dbSetUp) {
            $dbFile = $this->appRoot.'/../Fixture/sqlite/testdb.sqlite';
            $this->tempDbFile = $this->appRoot.'/database/testdb.sqlite';

            $fs = new Filesystem();
            if ($fs->exists($this->tempDbFile)) {
                $fs->remove($this->tempDbFile);
            }

            $fs->copy($dbFile, $this->tempDbFile);

            $this->dbSetUp = true;
        }
    }

    protected function getConnection()
    {
        $this->setUpTempDatabase();
        $dsn = 'sqlite:'.$this->tempDbFile;
        $this->dbh = new \PDO($dsn);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $this->dbh;
    }

    public function testApplyingPatchesWithSchemaChanges()
    {
        //create database connection
        $this->usePatches(array(
            '001.sql' => 'addcol_patch.sql',
            '002.sql' => 'select_patch.sql'
        ));
        //apply sql patch
        $output = $this->commandExecutor->execute(array('command' => 'dbpatcher:apply-patch'));
        $this->assertRegExp('/Applying patch 001.sql...registering...done/', $output);
        $this->assertRegExp('/Applying patch 002.sql...registering...done/', $output);

        $pdoCon = $this->getConnection();
        $stmt = $pdoCon->query('SELECT * FROM db_patch where name = "001.sql"');
        $this->assertEquals(1, count($stmt->fetchAll()));

        $stmt = $pdoCon->query('SELECT * FROM db_patch where name = "002.sql"');
        $this->assertEquals(1, count($stmt->fetchAll()));
    }
}
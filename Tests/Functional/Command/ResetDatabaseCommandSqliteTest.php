<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command;

use Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command\CommandTestCase;
use Symfony\Component\Filesystem\Filesystem;

class ResetDatabaseCommandSqliteTest extends CommandTestCase
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
    }

    protected function setupTestDb()
    {
        //setup the initial database file
        $fs = new Filesystem();
        $dbFile = $this->appRoot.'/../Fixture/sqlite/testdb.sqlite';
        $fs->copy($dbFile, $this->tempDbFile);
    }

    protected function getConnection()
    {
        $dsn = 'sqlite:'.$this->tempDbFile;
        $this->dbh = new \PDO($dsn);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $this->dbh;
    }

    protected function isTableExisting($tableName)
    {
        //make sure that the db_patch table exists
        $pdoCon = $this->getConnection();
        $stmt = $pdoCon->query(sprintf('SELECT name FROM sqlite_master WHERE type="table" AND name="%s";', $tableName));

        return count($stmt->fetchAll()) > 0;
    }

    public function testResetDatabaseWithExistingDBFileWithNoInitFile()
    {
        $this->setupTestDb();

        $fs = new Filesystem();
        $this->assertTrue($fs->exists($this->tempDbFile));

        $output = $this->commandExecutor->execute(array(
            'command' => 'dbpatcher:reset-database')
        );

        $this->assertRegExp('/Database has been reset!/', $output);
        $this->assertTrue($fs->exists($this->tempDbFile));

        $this->assertTrue($this->isTableExisting('db_patch'));
    }

    public function testResetDatabaseWithNoExistingDBFileAndWithNotInitFile()
    {
        $fs = new Filesystem();
        $this->assertFalse($fs->exists($this->tempDbFile));

        $output = $this->commandExecutor->execute(array(
            'command' => 'dbpatcher:reset-database')
        );

        $this->assertRegExp('/Database has been reset!/', $output);
        $this->assertTrue($fs->exists($this->tempDbFile));

        $this->assertTrue($this->isTableExisting('db_patch'));
    }

    public function testResetDatabaseWithExistingDBFileAndWithInitFile()
    {
        $this->setupTestDb();

        $fs = new Filesystem();
        $this->assertTrue($fs->exists($this->tempDbFile));
        $this->tempDbFile = $this->appRoot.'/database/testdb.sqlite';
        $initFile = $this->appRoot.'/../Fixture/sqlite/init.sql';

        $output = $this->commandExecutor->execute(array(
            'command' => 'dbpatcher:reset-database', 
            'init-file' => $initFile
        ));

        $this->assertRegExp('/Database has been reset!/', $output);

        $this->assertTrue($fs->exists($this->tempDbFile));
        $this->assertTrue($this->isTableExisting('db_patch'));
        $this->assertTrue($this->isTableExisting('account'));

    }

    public function testResetDatabaseWithPatches()
    {
        $fs = new Filesystem();
        $this->assertFalse($fs->exists($this->tempDbFile));
        $this->tempDbFile = $this->appRoot.'/database/testdb.sqlite';
        $initFile = $this->appRoot.'/../Fixture/sqlite/init.sql';


    }
}
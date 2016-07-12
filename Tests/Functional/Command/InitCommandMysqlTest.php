<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command;

use Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command\CommandTestCase;
use Symfony\Component\Filesystem\Filesystem;

class InitCommandMysqlTest extends CommandTestCase
{
    protected $env = 'mysql';
    protected $dbh = null;

    protected function getConnection()
    {
        if (is_null($this->dbh)) {
            $dsn = 'mysql:host=localhost;dbname=dbpatcher';
            $this->dbh = new \PDO($dsn, 'root', 'password');
        }

        return $this->dbh;
    }


    public function testInitDatabaseShouldCreateTheDBPatcherTable()
    {
        $this->commandExecutor->execute(array(
            'command' => 'dbpatcher:init'
        ));

        //check if the table has been created
        $pdoCon = $this->getConnection();
        $stmt = $pdoCon->query('SHOW TABLES LIKE "db_patch";');
        $this->assertEquals(1, count($stmt->fetchAll()));
    }

}
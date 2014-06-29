<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Unit\Command;

use Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command\ApplyDatabasePatchCommandTestCase;

class ApplyDatabasePatchCommandSqliteTest extends ApplyDatabasePatchCommandTestCase
{

    protected $env = 'sqlite';

    protected function getConnection()
    {
        $dbFile = $this->appRoot.'/../Fixture/sqlite/testdb.sqlite';

        $dsn = 'sqlite:'.$dbFile;
        $dbh = new \PDO($dsn);

        return $dbh;
    }

}
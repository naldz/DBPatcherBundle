<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command;

use Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command\ApplyDatabasePatchCommandTestCase;

class ApplyDatabasePatchCommandMysqlTest extends ApplyDatabasePatchCommandTestCase
{

    protected $env = 'mysql';

    protected function getConnection()
    {
        $dsn = 'mysql:host=localhost;dbname=dbpatcher';
        $dbh = new \PDO($dsn, 'root', 'password');
        
        return $dbh;
    }

}
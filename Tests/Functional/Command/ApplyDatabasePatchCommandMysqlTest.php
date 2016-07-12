<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command;

use Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command\ApplyDatabasePatchCommandTestCase;

class ApplyDatabasePatchCommandMysqlTest extends ApplyDatabasePatchCommandTestCase
{

    protected $env = 'mysql';

    public function setUp()
    {
        //create the table;
        $dbh = $this->getConnection();
        $dbh->exec("
            CREATE TABLE IF NOT EXISTS `db_patch` (
                `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT, 
                `name` varchar(50) NOT NULL, 
                `date_applied` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                PRIMARY KEY (`id`) 
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8; TRUNCATE TABLE db_patch;");

        parent::setUp();
    }

    protected function getConnection()
    {
        $dsn = 'mysql:host=localhost;dbname=dbpatcher';
        $dbh = new \PDO($dsn, 'root', 'password');
        
        return $dbh;
    }

}
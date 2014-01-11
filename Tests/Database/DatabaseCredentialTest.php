<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Patch;

use Naldz\Bundle\DBPatcherBundle\Database\DatabaseCredential;

class DatabaseCredentialTest extends \PHPUnit_Framework_TestCase
{

    private $dbHost = 'test_db_host';
    private $dbUser = 'test_db_user';
    private $dbPass = 'test_db_pass';
    private $dbName = 'test_db_name';

    protected function setUp()
    {
        $this->dbCred = new DatabaseCredential($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
    }
    
    public function testGettingOfCredentials()
    {

        $this->assertEquals($this->dbHost, $this->dbCred->getHost());
        $this->assertEquals($this->dbUser, $this->dbCred->getUser());
        $this->assertEquals($this->dbPass, $this->dbCred->getPassword());
        $this->assertEquals($this->dbName, $this->dbCred->getDatabaseName());
    }
    
}

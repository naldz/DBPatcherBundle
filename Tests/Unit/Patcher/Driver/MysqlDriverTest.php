<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Unit\Patcher\DsnPatcher;

use Naldz\Bundle\DBPatcherBundle\Patcher\Driver\MysqlDriver;

class MysqlDriverTest extends \PHPUnit_Framework_TestCase
{
    
    private function createDsnParserMock($creds = null)
    {
        $mock = $this->getMockBuilder('Naldz\\Bundle\\DBPatcherBundle\\Patcher\\DsnParser\\MysqlDsnParser')
            ->disableOriginalConstructor()
            ->getMock();

        if (!is_null($creds)) {
            $mock->expects($this->once())
                ->method('parse')
                ->will($this->returnValue($creds));
        }

        return $mock;
    }

    public function testGetConnection()
    {
        $creds = array(
            'prefix'    => 'mysql',
            'user'      => 'my_user',
            'password'  => 'my_pass',
            'host'      => 'example.com',
            'database'  => 'my_db'
        );

        $dsnParseMock = $this->createDsnParserMock($creds);
        $mysqlDriver = new MysqlDriver($dsnParseMock, 'dsn', '/usr/bin/mysql');

        $connection = $mysqlDriver->getConnection('Naldz\\Bundle\\DBPatcherBundle\\TestHelper\\Stub\\PDOStub');

        $this->assertEquals('mysql:host=example.com;dbname=my_db', $connection->getConnectionString());
        $this->assertEquals('my_user', $connection->getUser());
        $this->assertEquals('my_pass', $connection->getPassword());
    }

    public function testApplyPatch()
    {
        $creds = array(
            'prefix'    => 'mysql',
            'user'      => 'my_user',
            'password'  => 'my_pass',
            'host'      => 'example.com',
            'database'  => 'my_db'
        );

        $dsnParserMock = $this->createDsnParserMock($creds);
        $mysqlDriver = new MysqlDriver($dsnParserMock, 'dsn', '/usr/bin/mysql');

        $connection = $mysqlDriver->getConnection('Naldz\\Bundle\\DBPatcherBundle\\TestHelper\\Stub\\PDOStub');

        $processMock = $this->getMockBuilder('Symfony\Component\Process\Process')
            ->disableOriginalConstructor()
            ->getMock();
        
        $processMock->expects($this->once())
            ->method('run');
            
        $processMock->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        $mysqlDriver->applyPatch('/path/to/patch/file', $processMock);
    }

    // public function testInit()
    // {
    //     $creds = array(
    //         'prefix'    => 'mysql',
    //         'user'      => 'my_user',
    //         'password'  => 'my_pass',
    //         'host'      => 'example.com',
    //         'database'  => 'my_db'
    //     );

    //     $dsnParserMock = $this->createDsnParserMock($creds);
    //     $mysqlDriver = new MysqlDriver($dsnParserMock, 'dsn', '/usr/bin/mysql');

    //     $connection = $mysqlDriver->getConnection('Naldz\\Bundle\\DBPatcherBundle\\TestHelper\\Stub\\PDOStub');

    //     $processMock = $this->getMockBuilder('Symfony\Component\Process\Process')
    //         ->disableOriginalConstructor()
    //         ->getMock();

    //     $processMock->expects($this->once())
    //         ->method('isSuccessful')
    //         ->will($this->returnValue(true));

    //     $mysqlDriver->init($processMock);
    // }

}
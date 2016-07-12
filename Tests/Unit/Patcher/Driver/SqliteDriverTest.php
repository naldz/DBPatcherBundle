<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Unit\Patcher\DsnPatcher;

use Naldz\Bundle\DBPatcherBundle\Patcher\Driver\SqliteDriver;

class SqliteDriverTest extends \PHPUnit_Framework_TestCase
{
    
    private function createDsnParserMock($creds = null)
    {
        $mock = $this->getMockBuilder('Naldz\\Bundle\\DBPatcherBundle\\Patcher\\DsnParser\\SqliteDsnParser')
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
            'prefix'        => 'sqlite',
            'database_file' => '/path/to/dbfile.sqlite',
        );

        $dsnParserMock = $this->createDsnParserMock($creds);
        $sqliteDriver = new SqliteDriver($dsnParserMock, 'dsn', '/usr/bin/sqlite3');

        $connection = $sqliteDriver->getConnection('Naldz\\Bundle\\DBPatcherBundle\\TestHelper\\Stub\\PDOStub');

        $this->assertEquals('sqlite:/path/to/dbfile.sqlite', $connection->getConnectionString());
    }

    public function testApplyPatch()
    {
        $creds = array(
            'prefix'        => 'sqlite',
            'database_file' => '/path/to/dbfile.sqlite',
        );

        $dsnParseMock = $this->createDsnParserMock($creds);
        $sqliteDriver = new SqliteDriver($dsnParseMock, 'dsn', '/usr/bin/sqlite3');

        $connection = $sqliteDriver->getConnection('Naldz\\Bundle\\DBPatcherBundle\\TestHelper\\Stub\\PDOStub');

        $processMock = $this->getMockBuilder('Symfony\Component\Process\Process')
            ->disableOriginalConstructor()
            ->getMock();
        
        $processMock->expects($this->once())
            ->method('run');
            
        $processMock->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        $sqliteDriver->applyPatch('/path/to/patch/file', $processMock);
    }

    public function testResetDatabase()
    {
        $creds = array(
            'prefix'        => 'sqlite',
            'database_file' => '/path/to/dbfile.sqlite',
        );

        $dsnParseMock = $this->createDsnParserMock($creds);
        $sqliteDriver = new SqliteDriver($dsnParseMock, 'dsn', '/usr/bin/sqlite3');

        $connection = $sqliteDriver->getConnection('Naldz\\Bundle\\DBPatcherBundle\\TestHelper\\Stub\\PDOStub');

        $rmProcMock = $this->getMockBuilder('Symfony\Component\Process\Process')
            ->disableOriginalConstructor()
            ->getMock();
        $rmProcMock->expects($this->once())
            ->method('run');
            
        $rmProcMock->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        $createProcMock = $this->getMockBuilder('Symfony\Component\Process\Process')
            ->disableOriginalConstructor()
            ->getMock();
        $createProcMock->expects($this->once())
            ->method('run');
        $createProcMock->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        $initProcMock = $this->getMockBuilder('Symfony\Component\Process\Process')
            ->disableOriginalConstructor()
            ->getMock();
        $initProcMock->expects($this->once())
            ->method('run');
        $initProcMock->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        $sqliteDriver->resetDatabase('/path/to/patch/file', $rmProcMock, $createProcMock, $initProcMock);

    }

    public function testInit()
    {
        
    }
}
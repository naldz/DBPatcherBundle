<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Patch;

use Naldz\Bundle\DBPatcherBundle\Patch\DatabasePatcher;

class DatabasePatcherTest extends \PHPUnit_Framework_TestCase
{
    private $mysqlBin = '/usr/bin/mysql';
    private $patchDir = '/patch/dir';

    private $dbHost = 'test_db_host';
    private $dbUser = 'test_db_user';
    private $dbPass = 'test_db_pass';
    private $dbName = 'test_db_name';

    private $dbCredMock;
    private $dbPatcher;

    protected function setUp()
    {
        //mock the dbCred
        $this->dbCredMock = $this->getMockBuilder('Naldz\Bundle\DBPatcherBundle\Database\DatabaseCredential')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dbCredMock->expects($this->any())->method('getHost')->will($this->returnValue($this->dbHost));
        $this->dbCredMock->expects($this->any())->method('getUser')->will($this->returnValue($this->dbUser));
        $this->dbCredMock->expects($this->any())->method('getPassword')->will($this->returnValue($this->dbPass));
        $this->dbCredMock->expects($this->any())->method('getDatabaseName')->will($this->returnValue($this->dbName));

        $this->dbPatcher = new DatabasePatcher($this->dbCredMock, $this->patchDir, $this->mysqlBin);
    }
    
    public function testPatching()
    {
        $patchFile = '123.sql';
        $fullPatchFile = $this->patchDir.DIRECTORY_SEPARATOR.$patchFile;
        $cmdString = sprintf('%s -h%s -u%s -p%s %s < %s', $this->mysqlBin, $this->dbHost, $this->dbUser, $this->dbPass, $this->dbName, $fullPatchFile);
        //mock the process
        $processMock = $this->getMockBuilder('Symfony\Component\Process\Process')
            ->disableOriginalConstructor()
            ->getMock();
        
        $processMock->expects($this->once())
            ->method('run');
            
        $processMock->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));
        
        $this->dbPatcher->applyPatch('123.sql', $processMock);
    }
    
    public function testPatchingFailed()
    {
        $this->setExpectedException('RuntimeException');
        
        $patchFile = '123.sql';
        $fullPatchFile = $this->patchDir.DIRECTORY_SEPARATOR.$patchFile;
        $cmdString = sprintf('%s -h%s -u%s -p%s %s < %s', $this->mysqlBin, $this->dbHost, $this->dbUser, $this->dbPass, $this->dbName, $fullPatchFile);

        //mock the process
        $processMock = $this->getMockBuilder('Symfony\Component\Process\Process')
            ->disableOriginalConstructor()
            ->getMock();
        
        $processMock->expects($this->once())
            ->method('run');
            
        $processMock->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(false));
        
        $this->dbPatcher->applyPatch('123.sql', $processMock);
    }
    

}
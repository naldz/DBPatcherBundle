<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Patch;

use Naldz\Bundle\DBPatcherBundle\Patch\PatchRegistry;

class PatchRegistryTest extends \PHPUnit_Framework_TestCase
{
    
    private $nameField = 'name';
    private $dateAppliedField = 'date_applied';
    
    private $dbHost = 'test_db_host';
    private $dbUser = 'test_db_user';
    private $dbPass = 'test_db_pass';
    private $dbName = 'test_db_name';
    
    protected function setUp()
    {
        $this->patchRegistry = new PatchRegistry($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
    }
    
    public function testGetConnection()
    {
        $this->patchRegistry->setPdoClass('Naldz\Bundle\DBPatcherBundle\TestHelper\Stub\PDOStub');
        $conn = $this->patchRegistry->getConnection();
        
        $this->assertInstanceOf('Naldz\Bundle\DBPatcherBundle\TestHelper\Stub\PDOStub', $conn);
        $this->assertEquals('mysql:host=test_db_host;dbname=test_db_name', $conn->getConnectionString());
        $this->assertEquals($this->dbUser, $conn->getUser());
        $this->assertEquals($this->dbPass, $conn->getPassword());

    }
    
    public function testGettingOfRegisteredPatches()
    {
        
        $stmtMock = $this->getMock('Naldz\\Bundle\\DBPatcherBundle\\TestHelper\\Stub\\PDOStatementStub');
        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue(array(
                array($this->nameField => '123.sql', $this->dateAppliedField => '2013-01-01 00:00:01'),
                array($this->nameField => '456.sql', $this->dateAppliedField => '2013-01-02 00:00:02'),
                array($this->nameField => '789.sql', $this->dateAppliedField => '2013-01-03 00:00:03')
        )));
        $stmtMock->expects($this->once())->method('closeCursor');
        
        $pdoMock =$this->getMockBuilder('Naldz\\Bundle\\DBPatcherBundle\\TestHelper\\Stub\\PDOStub')
            ->disableOriginalConstructor()
            ->getMock();

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->will($this->returnValue($stmtMock));

        $patches = $this->patchRegistry->getRegisteredPatches($pdoMock);
        
        $expectedPatches = array(
            '123.sql' => '2013-01-01 00:00:01',
            '456.sql' => '2013-01-02 00:00:02',
            '789.sql' => '2013-01-03 00:00:03'
        );
        
        $this->assertEquals($expectedPatches, $patches);
    }
    
    public function testRegisteringOfPatch()
    {
        $patchName = 'patch123.sql';
        
        $stmtMock = $this->getMockBuilder('Naldz\Bundle\DBPatcherBundle\TestHelper\Stub\PDOStatementStub')
            ->disableOriginalConstructor()
            ->getMock();
            
        $stmtMock->expects($this->once())
            ->method('execute')
            ->with(array(':name' => $patchName));

        $pdoMock =$this->getMockBuilder('Naldz\\Bundle\\DBPatcherBundle\\TestHelper\\Stub\\PDOStub')
            ->disableOriginalConstructor()
            ->getMock();
        
        $pdoMock->expects($this->once())
            ->method('prepare')
            ->will($this->returnValue($stmtMock));
            
        $this->patchRegistry->registerPatch($patchName, $pdoMock);
        
    }
    
}

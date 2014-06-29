<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Unit\Patcher;

use Naldz\Bundle\DBPatcherBundle\Patcher\PatchRegistry;

class PatchRegistryTest extends \PHPUnit_Framework_TestCase
{
    private $nameField = 'name';
    private $dateAppliedField = 'date_applied';
    
    public function createDriver($con = null)
    {
        $mock = $this->getMock('Naldz\\Bundle\\DBPatcherBundle\\Patcher\\Driver\\PatcherDriverInterface');

        if (!is_null($con)) {
            $mock->expects($this->once())
                ->method('getConnection')
                ->will($this->returnValue($con));
        }

        return $mock;
    }

    public function testGettingOfRegisteredPatches()
    {
        $pdoMock =$this->getMockBuilder('Naldz\\Bundle\\DBPatcherBundle\\TestHelper\\Stub\\PDOStub')
            ->disableOriginalConstructor()
            ->getMock();

        $stmtMock = $this->getMock('Naldz\\Bundle\\DBPatcherBundle\\TestHelper\\Stub\\PDOStatementStub');
        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue(array(
                array($this->nameField => '123.sql', $this->dateAppliedField => '2013-01-01 00:00:01'),
                array($this->nameField => '456.sql', $this->dateAppliedField => '2013-01-02 00:00:02'),
                array($this->nameField => '789.sql', $this->dateAppliedField => '2013-01-03 00:00:03')
        )));
        $stmtMock->expects($this->once())->method('closeCursor');
        
        $pdoMock->expects($this->once())
            ->method('prepare')
            ->will($this->returnValue($stmtMock));

        $driver = $this->createDriver($pdoMock);

        $patchRegistry = new PatchRegistry();
        $patchRegistry->setDriver($driver);

        $patches = $patchRegistry->getRegisteredPatches();
        
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

        $driver = $this->createDriver($pdoMock);

        $patchRegistry = new PatchRegistry();
        $patchRegistry->setDriver($driver);

        $patchRegistry->registerPatch($patchName);
        
    }

}
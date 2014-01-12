<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Unit\Patch;

use Naldz\Bundle\DBPatcherBundle\Patch\PatchRepository;

class PatchRepositoryTest extends \PHPUnit_Framework_TestCase
{
    
    private $patchRepository;
    
    private $testDir = '/path/to/dir';
    
    protected function setUp()
    {
        $this->patchRepository = new PatchRepository($this->testDir);
    }
    
    protected function createFinderMock($fileNames=array())
    {
        $finderMock =$this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->disableOriginalConstructor()
            ->getMock();

        $iteratorMock = new \Symfony\Component\Finder\Tests\Iterator\Iterator($fileNames);
        $finderMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iteratorMock));
            
        $finderMock->expects($this->once())
            ->method('files')
            ->will($this->returnSelf());            
        $finderMock->expects($this->once())
            ->method('in')
            ->with($this->testDir)
            ->will($this->returnSelf());
        
        return $finderMock;
    }

    public function testGettingOfUnappliedPatches()
    {
        //mock the PatchRegistry
        $patchRegistryMock =$this->getMockBuilder('Naldz\\Bundle\\DBPatcherBundle\\Patch\\PatchRegistry')
            ->disableOriginalConstructor()
            ->getMock();

        $patchRegistryMock->expects($this->once())
            ->method('getRegisteredPatches')
            ->will($this->returnValue(array(
                '123.sql' => '2013-01-01 00:00:01',
                '789.sql' => '2013-01-03 00:00:03'
            )));

        //mock the finder
        $finderMock = $this->createFinderMock(array('123.sql','456.sql','789.sql'));
        $finderMock->expects($this->once())
            ->method('sortByName')
            ->will($this->returnSelf());

        $unappliedPatches = $this->patchRepository->getUnappliedPatches($patchRegistryMock, $finderMock);
        
        $this->assertEquals(array('456.sql'), $unappliedPatches);   
    }
    
    public function testFileExists()
    {
        $finderMock1 = $this->createFinderMock(array());
        $finderMock1->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1));
            
        $this->assertTrue($this->patchRepository->patchFileExists('123.sql', $finderMock1));
        
        $finderMock2 = $this->createFinderMock(array());
        $finderMock2->expects($this->once())
            ->method('count')
            ->will($this->returnValue(0));
            
        $this->assertFalse($this->patchRepository->patchFileExists('123.sql', $finderMock2));
    }

}

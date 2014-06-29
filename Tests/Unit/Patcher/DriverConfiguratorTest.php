<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Unit\Patcher;

use Naldz\Bundle\DBPatcherBundle\Patcher\DriverConfigurator;

class DriverConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    public function createDrivableObjectMock($driver = null)
    {
        $mock = $this->getMock('Naldz\\Bundle\\DBPatcherBundle\\Patcher\\DrivableInterface');

        if (!is_null($driver)) {
            $mock->expects($this->once())
                ->method('setDriver')
                ->with($driver);
        }

        return $mock;
    }

    public function createDriverInterfaceMock()
    {
        $mock = $this->getMock('Naldz\\Bundle\\DBPatcherBundle\\Patcher\\Driver\\PatcherDriverInterface');
        return $mock;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConfigurationWithInvalidDsnDriverType()
    {
        $dsn = 'sample_dsn';
        $drivableObjectMock = $this->createDrivableObjectMock();

        $driverConfigurator = new DriverConfigurator($dsn, array());
        $driverConfigurator->configure($drivableObjectMock);
    }

    public function testConfigurationWithValidDsnDriverType()
    {
        $dsn = 'mysql:cred_info';
        $driverMock = $this->createDriverInterfaceMock();
        $drivableObjectMock = $this->createDrivableObjectMock($driverMock);

        $driverConfigurator = new DriverConfigurator($dsn, array('mysql' => $driverMock));
        $driverConfigurator->configure($drivableObjectMock);
    }

}
<?php

namespace Naldz\Bundle\DBPatcherBundle\Patcher;

use Naldz\Bundle\DBPatcherBundle\Patcher\DrivableInterface;
use Naldz\Bundle\DBPatcherBundle\Patcher\DsnParser;

class DriverConfigurator
{

    private $dsn;
    private $driverMap;

    private $driver;

    public function __construct($dsn, $driverMap = array())
    {
        $this->dsn = $dsn;
        $this->driverMap = $driverMap;
    }

    public function configure(DrivableInterface $drivableObject)
    {
        if (is_null($this->driver)) {
            $dsnChunks = explode(':', $this->dsn);
            if (empty($dsnChunks)) {
                throw new \Exception('Unable to get driver type from dsn: '.$this->dsn);
            }

            $driverType = $dsnChunks[0];

            if (!isset($this->driverMap[$driverType])) {
                throw new \InvalidArgumentException('Unsupported driver: '.$driverType);
            }

            $this->driver = $this->driverMap[$driverType];
        }

        $drivableObject->setDriver($this->driver);
    }

}
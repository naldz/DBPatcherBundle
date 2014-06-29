<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Unit\Patcher;

use Naldz\Bundle\DBPatcherBundle\Patcher\DatabasePatcher;

class DatabasePatcherTest extends \PHPUnit_Framework_TestCase
{

    public function createDriverInterfaceMock($filePatch)
    {
        $mock = $this->getMock('Naldz\\Bundle\\DBPatcherBundle\\Patcher\\Driver\\PatcherDriverInterface');

        $mock->expects($this->once())
            ->method('applyPatch')
            ->with($filePatch);

        return $mock;
    }

    public function testApplyPatch()
    {
        $patchDir = '/patch/dir';
        $patchFile = 'patch.sql';

        $databasePatcher = new DatabasePatcher($patchDir);
        $databasePatcher->setDriver($this->createDriverInterfaceMock("$patchDir/$patchFile"));

        $databasePatcher->applyPatch($patchFile);
    }


}
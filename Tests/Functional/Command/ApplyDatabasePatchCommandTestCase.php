<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command;

use Symfony\Component\Filesystem\Filesystem;
use Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command\CommandTestCase;

abstract class ApplyDatabasePatchCommandTestCase extends CommandTestCase
{

    protected $patchFixtureDir;

    abstract protected function getConnection();

    public function setUp()
    {
        parent::setUp();

        $this->patchFixtureDir = $this->appRoot.'/../Fixture/patch';

        //clean up the database
        $pdoCon = $this->getConnection();
        $pdoCon->exec('DELETE FROM db_patch WHERE 1');

    }

    protected function usePatches($patchMap = array())
    {
        foreach ($patchMap as $patchAlias => $patchFixtureFilename) {
            $this->fs->copy($this->patchFixtureDir."/$patchFixtureFilename", $this->patchDir."/$patchAlias");
        }
    }

    public function testNonExistingPatchFileThrowsException()
    {
        $this->setExpectedException('RuntimeException');
        $this->commandExecutor->execute(array('command' => 'dbpatcher:apply-patch', 'patch-file' => 'unknown.sql'));
    }

    public function testExistingPatchRegistersSuccessfully()
    {

        $this->usePatches(array('001.sql' => 'select_patch.sql'));

        $output = $this->commandExecutor->execute(array('command' => 'dbpatcher:apply-patch', 'patch-file' => '001.sql'));

        $this->assertRegExp('/Applying patch 001.sql...registering...done/', $output);

        //check if the patch has been registered
        $pdoCon = $this->getConnection();

        $stmt = $pdoCon->query('SELECT * FROM db_patch where name = "001.sql"');
        $this->assertEquals(1, count($stmt->fetchAll()));
    }

    public function testLongRunningPatchDoNotThrowAnException()
    {
        $this->usePatches(array('001.sql' => 'sleep_patch.sql'));
        $output = $this->commandExecutor->execute(array('command' => 'dbpatcher:apply-patch', 'patch-file' => '001.sql'));
        $this->assertRegExp('/Applying patch 001.sql...registering...done/', $output);
    }

}
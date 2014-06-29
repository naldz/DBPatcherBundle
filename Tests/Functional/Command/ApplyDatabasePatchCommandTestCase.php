<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command;

use Symfony\Component\Filesystem\Filesystem;
use Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command\CommandTestCase;

abstract class ApplyDatabasePatchCommandTestCase extends CommandTestCase
{

    abstract protected function getConnection();

    public function setUp()
    {
        parent::setUp();

        //remove the cache files from the app
        $fs = new FileSystem();
        $fs->remove(array($this->appRoot.'/cache', $this->appRoot.'/logs'));

        //clean up the database
        $pdoCon = $this->getConnection();
        $pdoCon->exec('DELETE FROM db_patch WHERE 1');
    }

    public function testNonExistingPatchFileThrowsException()
    {
        $this->setExpectedException('RuntimeException');
        $this->commandExecutor->execute(array('command' => 'dbpatcher:apply-patch', 'patch-file' => 'unknown.sql'));
    }

    public function testExistingPatchRegistersSuccessfully()
    {
        $output = $this->commandExecutor->execute(array('command' => 'dbpatcher:apply-patch', 'patch-file' => 'sql_patch_1.sql'));

        $this->assertRegExp('/Applying patch sql_patch_1.sql...registering...done/', $output);

        //check if the patch has been registered
        $pdoCon = $this->getConnection();

        $stmt = $pdoCon->query('SELECT * FROM db_patch where name = "sql_patch_1.sql"');
        $this->assertEquals(1, count($stmt->fetchAll()));
    }
}
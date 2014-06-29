<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command;

use Naldz\Bundle\DBPatcherBundle\Tests\Functional\Command\CommandTestCase;
use Symfony\Component\Filesystem\Filesystem;

class CreateDatabasePatchCommandTest extends CommandTestCase
{
    
    protected $env = 'temp';
    protected $patchDir;

    public function setUp()
    {
        parent::setUp();
        $this->patchDir = __DIR__.'/../../../TestHelper/Fixture/temp_patch';
    }

    protected function tearDown()
    {
        if (is_dir($this->patchDir)) {
            array_map('unlink', glob($this->patchDir.'/*'));
        }
    }
    
    public function testCreatePatchFile()
    {
        $output = $this->commandExecutor->execute(array('command' => 'dbpatcher:create-patch'));

        $this->assertRegExp('/Created patch file/', $output);

        $patchFileName = substr($output, 19, 14);
        
        $fs = new Filesystem();
        $this->assertTrue($fs->exists($this->patchDir.DIRECTORY_SEPARATOR.$patchFileName));

    }
}

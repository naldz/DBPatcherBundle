<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Unit\Patcher\DsnPatcher;

use Naldz\Bundle\DBPatcherBundle\Patcher\DsnParser\SqliteDsnParser;

class SqliteDsnParserTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $this->parser = new SqliteDsnParser();
    }
    
    public function testParsingFullDsn()
    {
        $dsn = 'sqlite:/path/to/db.sqlite';
        $expectedChunks = array(
            'prefix'        => 'sqlite',
            'database_file' => '/path/to/db.sqlite'
        );

        $this->assertEquals($expectedChunks, $this->parser->parse($dsn));

    }
}
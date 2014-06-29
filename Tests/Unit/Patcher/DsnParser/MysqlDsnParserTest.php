<?php

namespace Naldz\Bundle\DBPatcherBundle\Tests\Unit\Patcher\DsnPatcher;

use Naldz\Bundle\DBPatcherBundle\Patcher\DsnParser\MysqlDsnParser;

class MysqlDsnParserTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $this->parser = new MysqlDsnParser();
    }
    
    public function testParsingFullDsn()
    {
        $dsn = 'mysql:my_user:my_pass@example.com:3307\my_db';
        $expectedChunks = array(
            'prefix'    => 'mysql',
            'user'      => 'my_user',
            'password'  => 'my_pass',
            'host'      => 'example.com',
            'port'      => '3307',
            'database'  => 'my_db'
        );

        $this->assertEquals($expectedChunks, $this->parser->parse($dsn));

    }

    public function testParsingDsnWithoutPort()
    {
        $dsn = 'mysql:my_user:my_pass@example.com\my_db';
        $expectedChunks = array(
            'prefix'    => 'mysql',
            'user'      => 'my_user',
            'password'  => 'my_pass',
            'host'      => 'example.com',
            'port'      => '',
            'database'  => 'my_db'
        );

        $this->assertEquals($expectedChunks, $this->parser->parse($dsn));
    }

    public function testParsingDsnWithoutPassword()
    {
        $dsn = 'mysql:my_user@example.com:3307\my_db';
        $expectedChunks = array(
            'prefix'    => 'mysql',
            'user'      => 'my_user',
            'password'  => '',
            'host'      => 'example.com',
            'port'      => '3307',
            'database'  => 'my_db'
        );

        $this->assertEquals($expectedChunks, $this->parser->parse($dsn));
    }

}
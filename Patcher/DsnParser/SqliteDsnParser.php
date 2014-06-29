<?php

namespace Naldz\Bundle\DBPatcherBundle\Patcher\DsnParser;

use Naldz\Bundle\DBPatcherBundle\Patcher\DsnParser\DsnParserInterface;

class SqliteDsnParser implements DsnParserInterface
{

    public function parse($dsn)
    {   

        $chunks = explode(':', $dsn);

        if (count($chunks) != 2) {
            throw new \Exception('Invalid Sqlite datasource name: '.$dsn);
        }

        return array(
            'prefix'        => $chunks[0], 
            'database_file' => $chunks[1]
        );
    }

}
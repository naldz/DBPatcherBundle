<?php

namespace Naldz\Bundle\DBPatcherBundle\Patcher\DsnParser;

use Naldz\Bundle\DBPatcherBundle\Patcher\DsnParser\DsnParserInterface;

class MysqlDsnParser implements DsnParserInterface
{

    public function parse($dsn)
    {   
        $result = array();

        $userHost = explode('@',$dsn);
        if (count($userHost) != 2) {
            throw new \Exception('Invalid Mysql datasource name: '.$dsn);
        }

        $leftChunk = $userHost[0];
        $rightChunk = $userHost[1];

        $leftMiniChunks = explode(':', $leftChunk);
        if (count($leftMiniChunks) < 2 || count($leftMiniChunks) > 3) {
            throw new \Exception('Invalid Mysql datasource name: '.$dsn);
        }

        $result = array(
            'prefix'    => $leftMiniChunks[0],
            'user'      => $leftMiniChunks[1]
        );

        $result['password'] = '';
        if (isset($leftMiniChunks[2])) {
            $result['password'] = $leftMiniChunks[2];
        }

        $rightMiniChunks = explode('\\', $rightChunk);
        if (count($rightMiniChunks) != 2) {
            throw new \Exception('Invalid Mysql datasource name: '.$dsn);
        }

        $result['database'] = $rightMiniChunks[1];

        //try to parse host and port
        $hostPort = explode(':', $rightMiniChunks[0]);
        $result['host'] = $hostPort[0];

        $result['port'] = '';
        if (isset($hostPort[1])) {
            $result['port'] = $hostPort[1];
        }

        return $result;
    }

}
<?php

namespace Naldz\Bundle\DBPatcherBundle\Patcher\DsnParser;

interface DsnParserInterface
{
    
    public function parse($dsn);

}
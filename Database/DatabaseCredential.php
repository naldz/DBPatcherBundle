<?php

namespace Naldz\Bundle\DBPatcherBundle\Database;

class DatabaseCredential
{
    private $host;
    private $user;
    private $password;
    private $dbName;
    
    public function __construct($host, $user, $password, $dbName)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->dbName = $dbName;
    }
    
    public function getHost()
    {
        return $this->host;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function getDatabaseName()
    {
        return $this->dbName;
    }
    
}
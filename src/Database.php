<?php
class Database
{
    public function __construct(private $host, private $user, private $password, private $dbname)
    {
    }

    public function connect()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";

        return new PDO($dsn, $this->user, $this->password);
    }
}

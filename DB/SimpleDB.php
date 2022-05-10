<?php

namespace GF\DB;

class SimpleDB {

    private $db = null;
    protected $connection = 'default';

    public function __construct($connection = null) {
        if ($connection instanceof \PDO) {
            $this->db = $connection;
        } else if ($connection != null) {
            $this->db = \GF\App::getInstance()->getDBConnection($connection);
            $this->connection = $connection;
        } else {
            $this->db = \GF\App::getInstance()->getDBConnection($this->connection);
        }
    }
}

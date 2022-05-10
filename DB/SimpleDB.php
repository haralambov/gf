<?php

namespace GF\DB;

class SimpleDB {

    private $db = null;
    protected $connection = 'default';
    private $smtm = null;
    private $params = array();
    private $sql;

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

    /**
     * @param mixed $sql 
     * @param array $params 
     * @param array $pdoOptions 
     * @return $this 
     */
    public function prepare($sql, $params = array(), $pdoOptions = array()) {
        $this->smtm = $this->db->prepare($sql, $pdoOptions);
        $this->params = $params;
        $this->sql = $sql;
        return $this;
    }

    /**
     * @param array $params 
     * @return $this 
     */
    public function execute($params = array()) {
        if ($params) {
            $this->params = $params;
        }
        $this->smtm->execute($this->params);
        return $this;
    }

    public function fetchAllAssoc() {
        return $this->smtm->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchRowAssoc() {
        return $this->smtm->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAllNum() {
        return $this->smtm->fetchAll(\PDO::FETCH_NUM);
    }

    public function fetchRowNum() {
        return $this->smtm->fetch(\PDO::FETCH_NUM);
    }

    public function fetchAllObj() {
        return $this->smtm->fetchAll(\PDO::FETCH_OBJ);
    }

    public function fetchRowObj() {
        return $this->smtm->fetch(\PDO::FETCH_OBJ);
    }

    public function fetchAllColumn($column) {
        return $this->smtm->fetchAll(\PDO::FETCH_COLUMN, $column);
    }

    public function fetchRowColumn($column) {
        return $this->smtm->fetch(\PDO::FETCH_COLUMN, $column);
    }

    public function fetchAllClass($class) {
        return $this->smtm->fetchAll(\PDO::FETCH_CLASS, $class);
    }

    public function fetchRowClass($class) {
        return $this->smtm->fetch(\PDO::FETCH_CLASS, $class);
    }

    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    public function getAffectedRows() {
        return $this->smtm->rowCount();
    }

    public function getSTMT() {
        return $this->smtm;
    }
}


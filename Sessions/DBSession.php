<?php

namespace GF\Sessions;

class DBSession extends \GF\DB\SimpleDB implements \GF\Sessions\ISession {

    private $sessionName;
    private $tableName;
    private $lifetime;
    private $path;
    private $domain;
    private $secure;
    private $sessionId;
    private $sessoinData = array();

    public function __construct($dbConnection, $name, $tableName = 'sessoins', $lifetime = 3600, $path = null, $domain = null, $secure = false) {
        parent::__construct($dbConnection);
        $this->tableName = $tableName;
        $this->sessionName = $name;
        $this->lifetime = $lifetime;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->sessionId = $_COOKIE[$name];

        if (strlen($this->sessionId) < 32) {
            $this->_startNewSession();
        } else if (!$this->_validateSession()) {
            $this->_startNewSession();
        }
    }

    public function getSessionId() { }

    public function saveSession() {
        if ($this->sessionId) {
            $this->prepare('UPDATE ' . $this->tableName . ' SET sess_date=?, valid_until=? WHERE sessid=?')
                ->execute(array(serialize($this->sessoinData), time() + $this->lifetime, $this->sessionId));
            setcookie($this->sessionName, $this->sessionId, time() + $this->lifetime, $this->path, $this->domain, $this->secure, true);
        }
    }

    public function destroySession() { }

    public function __get($name) {
        return $this->sessoinData[$name];
    }

    public function __set($name, $value) {
        $this->sessoinData[$name] = $value;
    }

    private function _validateSession() {
        if ($this->sessionId) {
            $d = $this->prepare(
                    'SELECT * FROM ' . $this->tableName . ' WHERE sessid=? AND valid_until<=?',
                    array($this->sessionId, time() + $this->lifetime)
                )
                ->execute()
                ->fetchAllAssoc();
            if (is_array($d) && count($d) == 1 && $d[0]) {
                $this->sessoinData = unserialize($d[0]['sess_data']);
                return true;
            }
        }
        return false;
    }

    private function _startNewSession() {
        $this->sessionId = md5(uniqid('gf', true));
        $this->prepare('INSERT INTO ' . $this->tableName . ' (sessid, valid_until) VALUES (?, ?)')
            ->execute(array($this->sessionId, time() + $this->lifetime));
        setcookie($this->sessionName, $this->sessionId, time() + $this->lifetime, $this->path, $this->domain, $this->secure, true);
    }
}

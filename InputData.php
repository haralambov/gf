<?php

namespace GF;

class InputData {
    private static $_instance = null;
    private $_get = array();
    private $_post = array();
    private $_cookies = array();

    private function __construct() {
        $this->_cookies = $_COOKIE;
    }

    public function setPost($ar) {
        if (is_array($ar)) {
            $this->_post = $ar;
        }
    }

    public function setGet($ar) {
        if (is_array($ar)) {
            $this->_get = $ar;
        }
    }

    public function hasGet($id) {
        return array_key_exists($id, $this->_get);
    }

    public function hasPost($name) {
        return array_key_exists($name, $this->_post);
    }

    public function hasCookie($name) {
        return array_key_exists($name, $this->_cookies);
    }

    public function get($id, $normalize = null, $default = null) {
        if ($this->hasGet($id)) {
            if ($normalize != null) {
                return \GF\Common::normalize($this->_get[$id], $normalize);
            }
            return $this->_get[$id];
        }
        return $default;
    }

    public function post($name, $normalize = null, $default = null) {
        if ($this->hasPost($name)) {
            if ($normalize != null) {
                return \GF\Common::normalize($this->_post[$name], $normalize);
            }
            return $this->_post[$name];
        }
        return $default;
    }

    public function cookie($name, $normalize = null, $default = null) {
        if ($this->hasCookie($name)) {
            if ($normalize != null) {
                return \GF\Common::normalize($this->_cookies[$name], $normalize);
            }
            return $this->_cookies[$name];
        }
        return $default;
    }

    /**
     * @return \GF\InputData
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new \GF\InputData();
        }
        return self::$_instance;
    }
}

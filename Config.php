<?php

namespace GF;

class Config
{
    private static $_instance = null;
    private $_configFolder = null;
    private $_configArray = array();

    private function __construct() {

    }

    /**
     * @return \GF\Config
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new \GF\Config();
        }
        return self::$_instance;
    }

    public function setConfigFolder($configFolder) {
        if (!$configFolder) {
            throw new \Exception('Empty config folder path');
        }

        $_configFolder = realpath($configFolder);
        if ($_configFolder != FALSE && is_dir($_configFolder) && is_readable($_configFolder)) {
            // clear old config data
            $this->_configArray = array();
            $this->_configFolder = $_configFolder . DIRECTORY_SEPARATOR;
        } else {
            throw new \Exception('Config directory read error: '. $_configFolder);
        }
    }

    public function __get($name)
    {
        if (!$this->_configArray[$name]) {
            $this->includeConfigFile($this->_configFolder . $name . '.php');
        }
        if (array_key_exists($name, $this->_configArray)) {
            return $this->_configArray[$name];
        }
        return null;
    }

    public function includeConfigFile($path) {
        if (!$path) {
            //TODO
            throw new \Exception;
        }
        $_file = realpath($path);
        if ($_file != FALSE && is_file($_file) && is_readable($_file)) {
            $_basename = explode('.php', basename($_file))[0];
            include $_file;
            $this->_configArray[$_basename] = $cnf;
        } else {
            //TODO
            throw new \Exception('Config file read error: '. $path);
        }
    }
}

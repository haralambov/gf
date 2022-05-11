<?php

namespace GF;

class View {
    private static $_instance = null;
    private $viewPath = null;
    private $viewDir = null;
    private $data = array();
    private $extension = '.php';

    private function __construct() {
        $this->viewPath = \GF\App::getInstance()->getConfig()->app['viewDirectory'];
        if ($this->viewPath == null) {
            $this->viewPath = realpath('../views/');
        }
    }

    public function setViewDirectory($path) {
        $path = trim($path);
        if ($path) {
            $path = realpath($path) . DIRECTORY_SEPARATOR;
            if (is_dir($path) && is_readable($path)) {
                $this->viewDir = $path;
            } else {
                //TODO
                throw new \Exception('View path', 500);
            }
        } else {
            //TODO
            throw new \Exception('View path', 500);
        }
    }

    public function display($name, $data = array(), $returnAsString = false) {
        if (is_array($data)) {
            $this->data = array_merge($this->data, $data);
        }

        if ($returnAsString) {
            return $this->_includeFile($name);
        } else {
            echo $this->_includeFile($name);
        }
    }

    private function _includeFile($file) {
        if ($this->viewDir == null) {
            $this->setViewDirectory($this->viewPath);
        }
        $p = str_replace('.', DIRECTORY_SEPARATOR, $file);
        $fl = $this->viewDir . $p . $this->extension;
        if (is_file($fl) && is_readable($fl)) {
            ob_start();
            include $fl;
            return ob_get_clean();
        } else {
            throw new \Exception('View ' . $file . ' cannot be included', 500);
        }
        return null;
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
        return $this->data[$name];
    }

    /** @return \GF\View  */
    public function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new \GF\View();
        }
        return self::$_instance;
    }
}

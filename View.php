<?php

namespace GF;

class View {
    private static $___instance = null;
    private $___viewPath = null;
    private $___viewDir = null;
    private $___data = array();
    private $___extension = '.php';
    private $___layoutParts = array();
    private $___layoutData = array();

    private function __construct() {
        $this->___viewPath = \GF\App::getInstance()->getConfig()->app['viewDirectory'];
        if ($this->___viewPath == null) {
            $this->___viewPath = realpath('../views/');
        }
    }

    public function setViewDirectory($path) {
        $path = trim($path);
        if ($path) {
            $path = realpath($path) . DIRECTORY_SEPARATOR;
            if (is_dir($path) && is_readable($path)) {
                $this->___viewDir = $path;
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
            $this->___data = array_merge($this->___data, $data);
        }

        if (count($this->___layoutParts) > 0) {
            foreach ($this->___layoutParts as $k => $v) {
                $r = $this->_includeFile($v);
                if ($r) {
                    $this->___layoutData[$k] = $r;
                }
            }
        }

        if ($returnAsString) {
            return $this->_includeFile($name);
        } else {
            echo $this->_includeFile($name);
        }
    }

    public function getLayoutData($name) {
        return $this->___layoutData[$name];
    }

    public function appendToLayout($key, $template) {
        if ($key && $template) {
            $this->___layoutParts[$key] = $template;
        } else {
            throw new \Exception('Layout requires valid key and template', 500);
        }
    }

    private function _includeFile($file) {
        if ($this->___viewDir == null) {
            $this->setViewDirectory($this->___viewPath);
        }
        $___fl = $this->___viewDir . str_replace('.', DIRECTORY_SEPARATOR, $file) . $this->___extension;
        if (is_file($___fl) && is_readable($___fl)) {
            ob_start();
            include $___fl;
            return ob_get_clean();
        } else {
            throw new \Exception('View ' . $file . ' cannot be included', 500);
        }
        return null;
    }

    public function __set($name, $value) {
        $this->___data[$name] = $value;
    }

    public function __get($name) {
        return $this->___data[$name];
    }

    /** @return \GF\View  */
    public static function getInstance() {
        if (self::$___instance == null) {
            self::$___instance = new \GF\View();
        }
        return self::$___instance;
    }
}

<?php

namespace GF;

class View {
    private static $_instance = null;
    private $viewPath = null;
    private $data = array();

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

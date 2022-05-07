<?php

namespace GF;

class FrontController
{
    private static $_instance = null;

    private function __construct() {
    }

    /**
     * @return \GF\FrontController
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new \GF\FrontController();
        }
        return self::$_instance;
    }

    public function dispatch() {
        $router = new \GF\Routers\DefaultRouter();
        $router->parse();
        $controller = $router->getController();
        $method = $router->getMethod();
        if ($controller == null) {
            $controller = $this->getDefaultController();
        }
        if ($method == null) {
            $method = $this->getDefaultMethod();
        }
        echo $controller . '<br>' . $method;
    }

    public function getDefaultController() {
        $controller = \GF\App::getInstance()->getConfig()->app['default_controller'];
        if ($controller) {
            return $controller;
        }
        return 'Index';
    }

    public function getDefaultMethod() {
        $method = \GF\App::getInstance()->getConfig()->app['default_method'];
        if ($method) {
            return $method;
        }
        return 'index';
    }
}

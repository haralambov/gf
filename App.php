<?php

namespace GF;

include_once 'Loader.php';

class App
{
    private static $_instance = null;
    private $_config = null;
    /**
     * @var \GF\FrontController
     */
    private $_frontController = null;
    private $router = null;
    private $_dbConnections = array();
    private $_session = null;

    private function __construct() {
        \GF\Loader::registerNamespace('GF', dirname(__FILE__) . DIRECTORY_SEPARATOR);
        \GF\Loader::registerAutoload();
        $this->_config = \GF\Config::getInstance();
    }

    public function setConfigFolder($path) {
        $this->_config->setConfigFolder($path);
    }

    public function getConfigFolder() {
        return $this->_config->getConfigFolder();
    }

    public function getRouter() {
        return $this->router;
    }

    public function setRouter($router) {
        $this->router = $router;
    }

    /**
     * @return \GF\Config
     */
    public function getConfig() {
        return $this->_config;
    }

    public function run() {
        // if config folder is not set, use default one
        if ($this->_config->getConfigFolder() == null) {
            $this->_config->setConfigFolder('../config');
        }
        $this->_frontController = \GF\FrontController::getInstance();
        if ($this->router instanceof \GF\Routers\IRouter) {
            $this->_frontController->setRouter($this->router);
        }
        else if ($this->router == 'JsonRPCRouter') {
            //TODO fix it when RPC is done
            $this->_frontController->setRouter(new \GF\Routers\DefaultRouter());
        }
        else if ($this->router == 'CLIRouter') {
            //TODO fix it when RPC is done
            $this->_frontController->setRouter(new \GF\Routers\DefaultRouter());
        } else {
            $this->_frontController->setRouter(new \GF\Routers\DefaultRouter());
        }

        $_sess = $this->_config->app['session'];
        if ($_sess['autostart']) {
            if ($_sess['type'] == 'native') {
                $this->setSession(new \GF\Sessions\NativeSession($_sess['name'], $_sess['lifetime'], $_sess['path'], $_sess['domain'], $_sess['secure']));
            } else if ($_sess['type'] == 'database') {
                $this->setSession(new \GF\Sessions\DBSession($_sess['dbConnection'], $_sess['name'], $_sess['dbTable'], $_sess['lifetime'], $_sess['path'], $_sess['domain'], $_sess['secure']));
            } else {
                throw new \Exception('No valid session');
            }
        }

        $this->_frontController->dispatch();
    }

    /** @return \GF\Sessions\ISession  */
    public function getSession() {
        return $this->_session;
    }

    public function setSession(\GF\Sessions\ISession $session) {
        $this->_session = $session;
    }

    public function getDBConnection($connection = 'default') {
        if (!$connection) {
            throw new \Exception('No connection identifier provided', 500);
        }
        if ($this->_dbConnections[$connection]) {
            return $this->_dbConnections[$connection];
        }
        $_cnf = $this->getConfig()->database;
        if (!$_cnf[$connection]) {
            throw new \Exception('No valid connection identifier is provided', 500);
        }
        $dbh = new \PDO(
            $_cnf[$connection]['connection_uri'],
            $_cnf[$connection]['username'],
            $_cnf[$connection]['password'],
            $_cnf[$connection]['pdo_options']
        );
        $this->_dbConnections[$connection] = $dbh;
        return $dbh;
    }

    /**
     * @return \GF\App
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new \GF\App();
        }
        return self::$_instance;
    }

    public function __destruct() {
        if ($this->_session != null) {
            $this->_session->saveSession();
        }
    }
}

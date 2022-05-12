<?php

namespace GF\Routers;

class JsonRPCRouter implements \GF\Routers\IRouter {

    private $_map = array();
    private $_requestId;

    public function __construct() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] != 'application/json') {
            throw new \Exception('Require json request', 400);
        }
    }

    public function setMethodsMap($ar) {
        if (is_array($ar)) {
            $this->_map = $ar;
        }
    }

    public function getURI() {
        if (!is_array($this->_map) || count($this->_map) == 0) {
            $ar = \GF\App::getInstance()->getConfig()->rpcRoutes;
            if (is_array($ar) && count($ar) > 0) {
                $this->_map = $ar;
            } else {
                throw new \Exception('Router require method map', 500);
            }
        }

        $request = json_decode(file_get_contents('php://input'), true);
        if (!is_array($request) || !isset($request['method'])) {
            throw new \Exception('Require json request', 400);
        } else {
            if ($this->_map[$request['method']]) {
                $this->_requestId = $request['id'];
                return $this->_map[$request['method']];
            } else {
                throw new \Exception('Method not found', 501);
            }
        }
    }

    public function getRequestId() {
        return $this->_requestId;
    }
}

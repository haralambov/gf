<?php

namespace GF;

class App
{
    private static $_instance = null;

    public function run() {
        echo 'ok';
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
}

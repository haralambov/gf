<?php

namespace GF;

final class Loader
{
    private static $namespaces = array();

    private function __construct()
    {
    }

    public static function registerAutoload() {
        spl_autoload_register(array('\GF\Loader', 'autoload'));
    }

    public static function autoload($class) {
        self::loadClass($class);
    }

    public static function registerNamespace($namespace, $path) {
        $namespace = trim($namespace);
        if (strlen($namespace)> 0) {
            if (!$path) {
                throw new \Exception('Invalid path');
            }
            $_path = realpath($path);
            if ($_path && is_dir($_path) && is_readable($_path)) {
                self::$namespaces[$namespace] = $_path . DIRECTORY_SEPARATOR;
            } else {
                //TODO
                throw new \Exception('Namespace directory read error: ' . $path);
            }
        } else {
            //TODO
            throw new \Exception('Invalid namespace: ' . $namespace);
        }
    }
}

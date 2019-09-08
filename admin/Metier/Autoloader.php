<?php
class Autoloader {
    public static function register() {
        spl_autoload_register([__CLASS__,'autoload']);
    }
    
    private static function autoload($class) {
        //$class = str_replace('\\', '', $class);
        require_once $class.'.php';
    }
}
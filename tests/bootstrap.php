<?php
spl_autoload_register(function($class) {
    
    // split the class into namespace parts
    $parts = explode('\\', $class);
    if (count($parts) == 1) {
        return;
    }
    
    // the eventual filename
    $file = implode(DIRECTORY_SEPARATOR, $parts) . '.php';
    
    // look for a tests file (probably org\bovigo)
    $tmp = __DIR__ . DIRECTORY_SEPARATOR . $file;
    if (is_readable($tmp)) {
        require_once $tmp;
        return;
    }
    
    // the package dir for the class
    $dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "{$parts[0]}.{$parts[1]}";
    
    // look for a package src file
    $tmp = $dir . DIRECTORY_SEPARATOR . 'src'. DIRECTORY_SEPARATOR . $file;
    if (is_readable($tmp)) {
        require_once $tmp;
        return;
    }
    
    // look for a package tests file
    $tmp = $dir . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $file;
    if (is_readable($tmp)) {
        require_once $tmp;
        return;
    }
    
    // look in the include-path
    $dirs = explode(PATH_SEPARATOR, get_include_path());
    foreach ($dirs as $dir) {
        $tmp = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_readable($tmp)) {
            require_once $tmp;
            return;
        }
    }
});

// load source files after autoload
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src.php';

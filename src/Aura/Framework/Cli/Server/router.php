<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Framework
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */

$root      = substr(__DIR__, 0, strrpos(__DIR__, 'package'));
$root      = $root . 'web';
$has_index = strpos(strtolower($_SERVER['REQUEST_URI']), '/index.php');

if (false === $has_index && file_exists($root . $_SERVER['REQUEST_URI'])) {
      return false;
}

// workaround for PHP web server issue
// https://bugs.php.net/bug.php?id=61286
if (false !== strpos($_SERVER['REQUEST_URI'], '.')) {
    if (false === $has_index) {
        $path = $_SERVER['REQUEST_URI'];
    } else {
        $path = substr($_SERVER['REQUEST_URI'], $has_index + 10);
    }

    if (false === ($end = strpos($path, '?'))) {
        $_SERVER['PATH_INFO'] = $path;
    } else {
        $_SERVER['PATH_INFO'] = substr($path, 0, $end);
    }
}

require_once 'index.php';

<?php
namespace Aura\Framework\Autoload;

use Aura\Autoload\Loader as Autoloader;
use Aura\Framework\System;

class Loader extends Autoloader
{
    public function prep(System $system)
    {
        // closure to read files in an isolated scope.
        // $system is for cached aura classmaps.
        $read = function ($file, $system) {
            return require $file;
        };
        
        // is there a cached class map for Aura packages?
        $file = $system->getTmpPath("cache/classmap.php");
        if (is_readable($file)) {
            // use a class map instead of prefixes/paths
            $classes = $read($file, $system);
            $this->setClasses($classes);
        } else {
            $this->add('Aura\Framework\\', $system->getPackagePath('Aura.Framework/src'));
        }
        
        // look for Composer namespaces
        $file = $system->getVendorPath('composer/autoload_namespaces.php');
        if (is_readable($file)) {
            $namespaces = $read($file, $system);
            foreach ($namespaces as $prefix => $paths) {
                foreach ((array) $paths as $path) {
                    $this->add($prefix, $path);
                }
            }
        }
        
        // look for Composer classmap
        $file = $system->getVendorPath('composer/autoload_classmap.php');
        if (is_readable($file)) {
            $classmap = $read($file, $system);
            $this->addClasses($classmap);
        }
    }
}

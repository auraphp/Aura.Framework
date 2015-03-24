<?php
/**
 *
 * This file is part of the Aura project for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Framework\Cli\CacheClassmap;

use Aura\Framework\Cli\AbstractCommand;
use Aura\Framework\System as System;

/**
 *
 * Caches the map of package source class files to `tmp/cache/classmap.php`.
 *
 * @package Aura.Framework
 *
 */
class Command extends AbstractCommand
{
    /**
     *
     * The System object.
     *
     * @var System
     *
     */
    protected $system;

    /**
     *
     * Sets the system object.
     *
     * @param System $system The system object.
     *
     */
    public function setSystem(System $system)
    {
        $this->system = $system;
    }

    /**
     *
     * Caches the map of package source class files to
     * `tmp/cache/classmap.php`.
     *
     * @return mixed
     *
     */
    public function action()
    {
        $this->stdio->outln("Caching package class map ... ");

        // the eventual class map
        $classmap = [];

        // get the list of all packages
        $package_list = file($this->system->getConfigPath('_packages'));

        // retain the actual path to the packages, and create a "fake" path
        // with a literal '$system' in it, for use in the cached file.
        $package_path = $this->system->getPackagePath();
        $package_fake = str_replace(
            $this->system->getRootPath(),
            '{$system}',
            $package_path
        );

        // go through each package in the system ...
        foreach ($package_list as $package_name) {

            $package_name = trim($package_name);
            $path = $this->system->getPackagePath("{$package_name}/src");
            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            $skip = strlen($path) + 1;
            foreach ($items as $file => $object) {
                $base = substr($file, $skip);
                if (preg_match('/^[A-Z].*\.php$/', $base)) {
                    $base = substr($base, 0, -4);
                    $base = str_replace(DIRECTORY_SEPARATOR, '\\', $base);
                    $class = $base;

                    // convert the file's *real* absolute path to a fake
                    // with '$system` in it. this allows us to cache file
                    // locations relative to $system and keep them in code
                    // repositories for deployment to other systems.
                    $file = str_replace($package_path, $package_fake, $file);
                    $classmap[$class] = $file;
                    $this->stdio->outln($class . ' => ' . $file);
                }
            }
        }

        // create and/or clear the cached classmap.php file
        $cache = $this->system->getTmpPath("cache/classmap.php");
        @mkdir(dirname($cache), 0777, true);

        // export the classmap, and convert single quotes to double quotes
        // so that $system is interpolated properly
        $output = "<?php return " . var_export($classmap, true) . ';';
        $output = str_replace("'", '"', $output);
        file_put_contents($cache, $output);
        $this->stdio->outln("Cached to $cache.");
        $this->stdio->outln('Done.');
    }
}

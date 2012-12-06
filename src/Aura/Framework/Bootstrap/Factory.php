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
namespace Aura\Framework\Bootstrap;

use Aura\Framework\Autoload\Loader;
use Aura\Framework\System;
use Exception;

/**
 * 
 * A bootstrapper for web and cli execution.
 * 
 * @package Aura.Framework
 * 
 */
class Factory
{
    protected $root;

    protected $map = [
        'cli' => 'Aura\Framework\Bootstrap\Cli',
        'web' => 'Aura\Framework\Bootstrap\Web',
    ];

    public function __construct($root = null, array $map = null)
    {
        if (! $root) {
            // system/package/Aura.Framework/src/Aura/Framework/Bootstrap/Factory.php
            $root = dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        }

        $this->root = $root;

        if ($map) {
            $this->map = array_merge($this->map, $map);
        }
    }

    public function newInstance($type, $mode = null)
    {
        $di = $this->prep($mode);
        $class = $this->map[$type];
        return $di->newInstance($class);
    }

    public function prep($mode)
    {
        // turn up error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', true);
        ini_set('html_errors', false);

        // create the system object
        require_once dirname(__DIR__) . '/System.php';
        $system = new System($this->root);

        // set the include path
        set_include_path($system->getIncludePath());

        // requires
        require_once $system->getPackagePath('Aura.Autoload/src.php');
        require_once $system->getPackagePath('Aura.Framework/src/Aura/Framework/Autoload/Loader.php');

        // set the DI container object
        $di = require_once $system->getPackagePath('Aura.Di/scripts/instance.php');

        // set the autoloader
        $loader = new Loader;
        $loader->prep($system);
        $loader->register();

        // set framework services
        $di->set('framework_system', $system);
        $di->set('framework_loader', $loader);

        // get the config mode
        if (! $mode) {
            $file = $system->getConfigPath('_mode');
            if (is_readable($file)) {
                $mode = trim(file_get_contents($file));
            } else {
                $mode = 'default';
            }
        }

        // function to read config files in isolated scope
        $read = function ($file) use ($di, $system, $loader) {
            require $file;
        };

        // read config files
        $cache = $this->readCacheConfig($system, $read, $mode);
        if (! $cache) {
            $this->readPackageConfig($system, $read, $mode);
        }
        $this->readSystemConfig($system, $read, $mode);

        // lock the container
        $di->lock();

        // done!
        return $di;
    }

    /**
     * 
     * Reads the cached config file, if any.
     * 
     * @return bool True if there was a cached config file, false if not.
     * 
     */
    public function readCacheConfig(System $system, callable $read, $mode)
    {
        $file = $system->getTmpPath("cache/config/{$mode}.php");
        if (is_readable($file)) {
            $read($file);
            return true;
        }
    }

    /**
     * 
     * Reads the system config file.
     * 
     * @return void
     * 
     */
    public function readSystemConfig(System $system, callable $read, $mode)
    {
        $file = $system->getConfigPath("{$mode}.php");
        if (is_readable($file)) {
            $read($file);
        }
    }

    /**
     * 
     * Reads each package config file for the mode.
     * 
     * @return void
     * 
     */
    public function readPackageConfig(System $system, callable $read, $mode)
    {
        $package_path = $system->getPackagePath();
        $package_list = file($system->getConfigPath('_packages'));
        foreach ($package_list as $package_name) {

            $package_name = trim($package_name);
            if (! $package_name) {
                continue;
            }

            // load its default config file, if any
            $package_file = $package_path . DIRECTORY_SEPARATOR
                          . $package_name . DIRECTORY_SEPARATOR
                          . 'config' . DIRECTORY_SEPARATOR
                          . 'default.php';

            if (is_readable($package_file)) {
                $read($package_file);
            }

            // load its config-mode-specific file, if any
            if ($mode == 'default') {
                continue;
            }

            $package_file = $package_path . DIRECTORY_SEPARATOR
                            . 'config' . DIRECTORY_SEPARATOR
                            . "{$mode}.php";

            if (is_readable($package_file)) {
                $read($package_file);
            }
        }
    }
}

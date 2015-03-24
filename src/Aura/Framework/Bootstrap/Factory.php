<?php
/**
 *
 * This file is part of the Aura project for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Framework\Bootstrap;

use Aura\Di\Config;
use Aura\Di\Container;
use Aura\Di\Forge;
use Aura\Framework\Autoload\Loader;
use Aura\Framework\System;
use Aura\Router\Map as RouterMap;
use Aura\Router\DefinitionFactory;
use Aura\Router\RouteFactory;
use Exception;

/**
 *
 * A bootstrapper factory.
 *
 * @package Aura.Framework
 *
 */
class Factory
{
    /**
     *
     * The path to the system root.
     *
     * @var string
     *
     */
    protected $root;

    /**
     *
     * An array of app types to bootstrap classes.
     *
     * @var array
     *
     */
    protected $map = [
        'cli' => 'Aura\Framework\Bootstrap\Cli',
        'web' => 'Aura\Framework\Bootstrap\Web',
    ];

    /**
     *
     * Constructor.
     *
     * @param string $root The path to the system root.
     *
     * @param array $map An override map of app types to bootstrap classes.
     *
     */
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

    /**
     *
     * Returns a new bootstrap instance.
     *
     * @param string $type The app type.
     *
     * @param string $mode The config mode to use.
     *
     * @param bool $silent_loader Force the autoloader to MODE_SILENT; this is
     * useful primarily for testing purposes.
     *
     * @return object A bootstrapper.
     *
     */
    public function newInstance($type, $mode = null, $silent_loader = false)
    {
        $di = $this->prep($mode, $silent_loader);
        $class = $this->map[$type];
        return $di->newInstance($class);
    }

    /**
     *
     * Preps the framework for bootstrapping: creates foundation objects,
     * loads configurations, etc.
     *
     * @param string $mode The config mode.
     *
     * @param bool $silent_loader Force the autoloader to MODE_SILENT; this is
     * useful primarily for testing purposes.
     *
     * @return \Aura\Di\Container A dependency injection container.
     *
     */
    public function prep($mode = null, $silent_loader = false)
    {
        // turn up error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', true);
        ini_set('html_errors', false);

        // create the system object and prepend to include path
        require_once dirname(__DIR__) . '/System.php';
        $system = new System($this->root);
        set_include_path($system->getIncludePath() . PATH_SEPARATOR . get_include_path());

        // create the autoloader
        require_once $system->getPackagePath('Aura.Autoload/src.php');
        require_once $system->getPackagePath('Aura.Framework/src/Aura/Framework/Autoload/Loader.php');
        $loader = new Loader;
        $loader->prep($system);
        if ($silent_loader) {
            $loader->setMode($loader::MODE_SILENT);
        }
        $loader->register();

        // create the router
        require_once $system->getPackagePath('Aura.Router/src.php');
        $router = new RouterMap(new DefinitionFactory, new RouteFactory);

        // create the DI container
        $di = new Container(new Forge(new Config));

        // set framework services
        $di->set('framework_system', $system);
        $di->set('framework_loader', $loader);
        $di->set('router_map', $router);

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
        $read = function ($file) use ($di, $system, $loader, $router) {
            require $file;
        };

        // read package config files
        $cache = $this->readCacheConfig($system, $read, $mode);
        if (! $cache) {
            $this->readPackageConfig($system, $read, $mode);
        }

        // read system config files
        $this->readSystemConfig($system, $read, 'default');
        if ($mode != 'default') {
            $this->readSystemConfig($system, $read, $mode);
        }

        // lock the container
        $di->lock();

        // create a temp router; this captures the config params for routes
        $temp_router = $di->newInstance('Aura\Router\Map');

        // prepend the temp router configs to the existing router service so
        // that we have backwards compat with existing configs
        $router->prependMap($temp_router);

        // done!
        return $di;
    }

    /**
     *
     * Reads the cached config file, if any.
     *
     * @param System $system A system object.
     *
     * @param callable $read A callable to read configs in a limited scope.
     *
     * @param string $mode The config mode.
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
     * @param System $system A system object.
     *
     * @param callable $read A callable to read configs in a limited scope.
     *
     * @param string $mode The config mode.
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
     * @param System $system A system object.
     *
     * @param callable $read A callable to read configs in a limited scope.
     *
     * @param string $mode The config mode.
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
                          . $package_name . DIRECTORY_SEPARATOR
                          . 'config' . DIRECTORY_SEPARATOR
                          . "{$mode}.php";

            if (is_readable($package_file)) {
                $read($package_file);
            }
        }
    }
}

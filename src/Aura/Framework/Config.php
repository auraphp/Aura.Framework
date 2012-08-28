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
namespace Aura\Framework;

use Aura\Autoload\Loader;
use Aura\Di\Container;

/**
 * 
 * Loads config values from package and system config files.
 * 
 * @package Aura.Framework
 * 
 */
class Config
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
     * The autoloader object.
     * 
     * @var Aura\Autoloader\Loader
     * 
     */
    protected $loader;

    /**
     * 
     * The dependency injection container.
     * 
     * @var Aura\Di\Container
     * 
     */
    protected $di;

    /**
     * 
     * Config files that have been loaded.
     * 
     * @var array
     * 
     */
    protected $files;

    /**
     * 
     * The config mode.
     * 
     * @var string
     * 
     */
    protected $mode;

    /**
     * 
     * Constructor.
     * 
     * @param System $system The System object.
     * 
     * @param Loader $loader The autoloader object.
     * 
     * @param Container $di The dependency injection container.
     * 
     */
    public function __construct(System $system, Loader $loader, Container $di)
    {
        $this->system = $system;
        $this->loader = $loader;
        $this->di     = $di;
        $this->mode   = empty($_ENV['AURA_CONFIG_MODE'])
                      ? 'default'
                      : $_ENV['AURA_CONFIG_MODE'];
    }

    /**
     * 
     * Returns the config mode.
     * 
     * @return string The config mode.
     * 
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * 
     * Returns the list of config files that have been loaded.
     * 
     * @return array The loaded config files.
     * 
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * 
     * Loads config files, either from the cache or from the packages, and
     * for the system mode config file.
     * 
     * @return void
     * 
     */
    public function exec()
    {
        $cache_file = $this->getCacheFile();
        if ($cache_file) {
            $this->load($cache_file);
        } else {
            $this->loadFromPackages();
        }

        $this->loadMode();
    }

    /**
     * 
     * Gets the name of the config cache file.
     * 
     * @return mixed The config file path, or null if not readable.
     * 
     */
    public function getCacheFile()
    {
        $file = $this->system->getTmpPath("cache/config/{$this->mode}.php");
        if (is_readable($file)) {
            return $file;
        }
    }

    /**
     * 
     * Loads each package config file for the mode.
     * 
     * @return void
     * 
     */
    public function loadFromPackages()
    {
        $package_glob = $this->system->getPackagePath('*');
        $package_list = glob($package_glob, GLOB_ONLYDIR);
        foreach ($package_list as $package_path) {

            // load its default config file, if any
            $package_file = $package_path . DIRECTORY_SEPARATOR
                          . 'config' . DIRECTORY_SEPARATOR
                          . 'default.php';
            if (is_readable($package_file)) {
                $this->load($package_file, $this->system, $this->loader, $this->di);
            }

            // load its config-mode-specific file, if any
            if ($this->mode == 'default') {
                continue;
            }

            $package_file = $package_path . DIRECTORY_SEPARATOR
                            . 'config' . DIRECTORY_SEPARATOR
                            . "{$config_mode}.php";
            if (is_readable($package_file)) {
                $this->load($package_file);
            }
        }
    }

    /**
     * 
     * Loads the system-level config file for the current mode.
     * 
     * @return void
     * 
     */
    public function loadMode()
    {
        $file = $this->system->getConfigPath("{$this->mode}.php");
        if (is_readable($file)) {
            $this->load($file);
        }
    }

    /**
     * 
     * Loads a config file in a limited scope.
     * 
     * @param string $file The config file to load.
     * 
     * @return void
     * 
     */
    public function load($file)
    {
        $system = $this->system->getRootPath();
        $loader = $this->loader;
        $di = $this->di;
        require $file;
        $this->files[] = $file;
    }
}

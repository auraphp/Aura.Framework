<?php
/**
 *
 * This file is part of the Aura project for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Framework\Cli\CacheConfig;

use Aura\Framework\Cli\AbstractCommand;
use Aura\Framework\System as System;

/**
 *
 * Caches all mode-specific package config files to
 * `tmp/cache/config/{$mode}.php`.
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
     * Caches the mode-specific package config files to
     * `tmp/cache/config/{$mode}.php`.
     *
     * @return mixed
     *
     */
    public function action()
    {
        if (! isset($this->params[0])) {
            $this->stdio->errln('Please specify a config mode to cache.');
            return 1;
        }

        $mode = $this->params[0];

        // create and/or clear the cached config-mode file
        $cache = $this->system->getTmpPath("cache/config/$mode.php");
        @mkdir(dirname($cache), 0777, true);
        file_put_contents($cache, "<?php\n");
        $this->stdio->outln("Caching '$mode' config mode to $cache ... ");

        // get the list of all packages in the system
        $package_list = file($this->system->getConfigPath('_packages'));

        // go through each package in the system
        foreach ($package_list as $package_name) {
            // find its directory
            $package_name = trim($package_name);
            $package_dir = $this->system->getPackagePath($package_name);
            // load its default config
            file_put_contents($cache, $this->read($package_dir, 'default'), FILE_APPEND);
            // load its mode-specific config
            if ($mode != 'default') {
                file_put_contents($cache, $this->read($package_dir, $mode), FILE_APPEND);
            }
        }

        $this->stdio->outln('Done.');
    }

    /**
     *
     * Reads a config file from a package directory; replaces __DIR__ and
     * __FILE__ constants with string values so that the original values
     * are honored regardless of where the cached configs are placed.
     *
     * @param string $package_dir The package directory.
     *
     * @param string $mode The config mode to read.
     *
     * @return string The config file, with __DIR__ and
     *
     */
    protected function read($package_dir, $mode)
    {
        // is there a mode-specific config file?
        $file = $package_dir . DIRECTORY_SEPARATOR . "config/$mode.php";
        if (! file_exists($file)) {
            // nope
            return;
        }

        // read the mode-specific config file
        $this->stdio->outln($file);

        // get the file contents
        $src = file_get_contents($file);

        // strip off the opening PHP tag
        $src = preg_replace('/^\s*\<\?php/m', '', $src);

        // replace __DIR__ with string literal for dirname($file),
        // relative to the $system directory
        $__dir__ = str_replace(
            $this->system->getRootPath(),
            '{$system}',
            '"' . dirname($file) . '"'
        );
        $src = str_replace('__DIR__', $__dir__, $src);

        // replace __FILE__ with string literal for $file,
        // relative to the $system directory
        $__file__ = str_replace(
            $this->system->getRootPath(),
            '{$system}',
            '"' . $file . '"'
        );
        $src = str_replace('__FILE__', $__file__, $src);

        // add a leading comment about the file source
        $src = "// " . str_pad('', strlen($__file__), '-') . PHP_EOL
             . "// $__file__" . PHP_EOL
             . "// " . PHP_EOL
             . $src;

        // done!
        return trim($src) . PHP_EOL . PHP_EOL . PHP_EOL;
    }
}

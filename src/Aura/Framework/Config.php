<?php
namespace Aura\Framework;
use Aura\Autoload\Loader;
use Aura\Di\Manager;

class Config
{
    public $system;
    
    public $loader;
    
    public $di;
    
    public $files;
    
    public function __construct(System $system, Loader $loader, Manager $di)
    {
        $this->system = $system;
        $this->loader = $loader;
        $this->di     = $di;
        $this->mode   = empty($_ENV['AURA_CONFIG_MODE'])
                      ? 'default'
                      : $_ENV['AURA_CONFIG_MODE'];
    }
    
    public function getMode()
    {
        return $this->mode;
    }
    
    public function getFiles()
    {
        return $this->files;
    }
    
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
    
    public function getCacheFile()
    {
        $file = $this->system->getTmpPath("cache/config/{$this->mode}.php");
        if (is_readable($file)) {
            return $file;
        }
    }
    
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
    
    public function loadMode()
    {
        $file = $this->system->getConfigPath("{$this->mode}.php");
        if (is_readable($file)) {
            $this->load($file);
        }
    }
    
    public function load($file)
    {
        $system = $this->system->getRootPath();
        $loader = $this->loader;
        $di = $this->di;
        require $file;
        $this->files[] = $file;
    }
}
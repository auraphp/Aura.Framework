<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @package Aura.Framework
 * 
 */
namespace Aura\Framework;
use Aura\Autoload\Loader;
use Aura\Di\Manager as DiManager;
use Aura\Di\Forge as DiForge;
use Aura\Di\Config as DiConfig;

class Bootstrap
{
    protected $system;
    
    protected $loader;
    
    protected $config;
    
    protected $di;
    
    public function exec()
    {
        // turn up error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', true);
        ini_set('html_errors', false);

        // create the system object
        require __DIR__ . DIRECTORY_SEPARATOR . 'System.php';
        $root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $this->system = new System($root);
        
        // set the include path
        set_include_path($this->system->getIncludePath());

        // autoloader
        $autoload_src = $this->system->getPackagePath('Aura.Autoload/src.php');
        require $autoload_src;
        $this->loader = new Loader;
        $this->loader->register();
        
        // load the class map, if any
        $classmap = $this->system->getTmpPath("cache/classmap.php");
        if (is_readable($classmap)) {
            // load all classes from a map
            $classes = $this->load($classmap);
            $this->loader->setClasses($classes);
        } else {
            // add namespace prefixes
            $this->loader->add('Aura\Framework\\', $this->system->getPackagePath('Aura.Framework/src'));
            $this->loader->add('Aura\Di\\', $this->system->getPackagePath('Aura.Di/src'));
        }
        
        // set the DI container object
        $this->di = new DiManager(new DiForge(new DiConfig));
        
        // set the config object
        $this->config = new Config($this->system, $this->loader, $this->di);
        
        // add the bootstrap objects to the container
        $this->di->set('framework_config', $this->config);
        $this->di->set('framework_loader', $this->loader);
        $this->di->set('framework_system', $this->system);
        
        // load configs
        $this->config->exec();
        
        // lock the container
        $this->di->lock();
        
        // done!
    }
    
    public function execWeb()
    {
        try {
            $this->exec();
            $front = $this->di->get('web_front');
            $response = $front->exec();
            $response->send();
        } catch (Exception $e) {
            echo $e . PHP_EOL;
            exit(1);
        }
    }
    
    public function execCli($class)
    {
        try {
            $this->exec();
            $context = $this->di->get('cli_context');
            $context->shiftArgv();
            $command = $this->di->newInstance($class);
            $command->exec();
        } catch (Exception $e) {
            echo $e . PHP_EOL;
            exit(1);
        }
    }
    
    public function load($file)
    {
        $system = $this->system->getRootPath();
        $loader = $this->loader;
        $di = $this->di;
        return require $file;
    }
}

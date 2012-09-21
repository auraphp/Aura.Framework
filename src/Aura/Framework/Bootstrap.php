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
use Aura\Di\Container as DiContainer;
use Aura\Di\Forge as DiForge;
use Aura\Di\Config as DiConfig;

/**
 * 
 * A bootstrapper for web and cli execution.
 * 
 * @package Aura.Framework
 * 
 */
class Bootstrap
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
     * The Config object.
     * 
     * @var Config
     * 
     */
    protected $config;

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
     * Execution setup method.
     * 
     * @return void
     * 
     */
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

        // set the loader object
        $this->setLoader();
        
        // set the DI container object
        $this->di = new DiContainer(new DiForge(new DiConfig));

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

    protected function setLoader()
    {
        // require the loader class files
        require $this->system->getPackagePath('Aura.Autoload/src.php');
        
        // create and register the loader
        $this->loader = new Loader;
        $this->loader->register();

        // is there a cached class map?
        $classmap = $this->system->getTmpPath("cache/classmap.php");
        if (is_readable($classmap)) {
            // load all classes from a map
            $classes = $this->load($classmap);
            $this->loader->setClasses($classes);
            // done!
            return;
        }
        
        // add namespace prefixes
        $this->loader->add('Aura\Framework\\', $this->system->getPackagePath('Aura.Framework/src'));
        $this->loader->add('Aura\Di\\', $this->system->getPackagePath('Aura.Di/src'));
    }
    
    /**
     * 
     * Execute bootstrap in a web context.
     * 
     * @return void
     * 
     */
    public function execWeb()
    {
        try {
            $this->exec();
            $front = $this->di->get('web_front');
            $response = $front->exec();
            $transport = $this->di->get('http_transport');
            $transport->sendResponse($response);
        } catch (Exception $e) {
            echo $e . PHP_EOL;
            exit(1);
        }
    }

    /**
     * 
     * Execute bootstrap in a CLI context.
     * 
     * @param string $class The command class to instantiate and execute.
     * 
     * @return void
     * 
     */
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

    /**
     * 
     * Require a file in a limited scope with variables for `$system`,
     * `$loader`, and `$di`. Generally for loading PHP-based config files.
     * 
     * @param string $file The file to require.
     * 
     * @return mixed
     * 
     */
    public function load($file)
    {
        $system = $this->system->getRootPath();
        $loader = $this->loader;
        $di = $this->di;
        return require $file;
    }
}

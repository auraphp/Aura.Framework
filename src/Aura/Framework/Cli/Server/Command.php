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
namespace Aura\Framework\Cli\Server;

use Aura\Framework\Cli\AbstractCommand;
use Aura\Cli\Option;
use Aura\Framework\System;

/**
 * 
 * Setup and run a development server.
 * 
 * @package Aura.Framework
 * 
 */
class Command extends AbstractCommand
{
    /**
     * 
     * The path to the PHP executable.
     * 
     * @var string
     * 
     */
    protected $php = 'php';

    /**
     * 
     * A System object.
     * 
     * @var System
     * 
     */
    protected $system;

    /**
     * 
     * Getopt definitions.
     * 
     * @var array
     * 
     */
    protected $options = [
        'port' => [
            'long'    => 'port',
            'short'   => 'p',
            'param'   => Option::PARAM_REQUIRED,
            'multi'   => false,
            'default' => 8000,
        ],
    ];

    /**
     *
     * Sets the path to the PHP executable.
     *
     * @param string $php The path to PHP.
     *
     * @return void
     * 
     */
    public function setPhp($php)
    {
        $this->php = $php;
    }

    /**
     *
     * Sets the System object.
     *
     * @param System $system
     *
     * @return void
     * 
     */
    public function setSystem($system)
    {
        $this->system = $system;
    }

    /**
     * 
     * Setup and run the server.
     * 
     * @return void
     * 
     */
    public function action()
    {
        $msg = "Starting the Aura development server @ "
             . "http://localhost:{$this->getopt->port}/";

        $this->stdio->outln($msg);

        // set the process elements
        $root   = $this->system->getWebPath();
        $router = $this->system->getPackagePath('Aura.Framework/scripts/router.php');
        $cmd    = "{$this->php} -S 0.0.0.0:{$this->getopt->port} {$router}";
        $spec   = [0 => STDIN, 1 => STDOUT, 2 => STDERR];

        // run the command as a process
        $process = proc_open($cmd, $spec, $pipes, $root);
        proc_close($process);
    }
}

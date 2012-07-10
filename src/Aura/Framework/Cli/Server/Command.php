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
     * Set the path to the PHP executable.
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
     * Setup and run the server.
     * 
     * @return void
     * 
     */
    public function action()
    {
        $url = "http://localhost:{$this->getopt->port}/";
        $msg = "Starting the Aura development server @ {$url}";

        $this->stdio->outln($msg);

        $root = substr(__DIR__, 0, strrpos(__DIR__, 'package'));
        $root = $root . 'web';

        // change to the web root directory
        chdir($root);

        $router = __DIR__ . DIRECTORY_SEPARATOR . 'router.php';
        $cmd    = "{$this->php} -S 0.0.0.0:{$this->getopt->port} {$router}";
        $pipe   = popen($cmd, 'r');

        while (! feof($pipe)) {
            $this->stdio->outln(fread($pipe, 2048));
        }

        pclose($pipe);
    }
}

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
     * @var string The path to the PHP executable.
     * 
     */
    protected $php = 'php';

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
     * @param string $path The path to PHP.
     *
     *
     */
    public function phpPath($path)
    {
        $this->php = $path;
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

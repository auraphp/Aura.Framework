<?php
/**
 *
 * This file is part of the Aura project for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Framework\Bootstrap;

use Aura\Cli\Context;
use Aura\Framework\Cli\Factory as CliFactory;

/**
 *
 * A bootstrapper for CLI apps.
 *
 * @package Aura.Framework
 *
 */
class Cli
{
    /**
     *
     * A CLI context object.
     *
     * @var Context
     *
     */
    protected $context;

    /**
     *
     * A CLI command factory.
     *
     * @var CliFactory
     *
     */
    protected $factory;

    /**
     *
     * Constructor.
     *
     * @param Context $context The CLI context.
     *
     * @param CliFactory $factory A factory for CLI commands.
     *
     */
    public function __construct(
        Context $context,
        CliFactory $factory
    ) {
        $this->context = $context;
        $this->factory = $factory;
    }

    /**
     *
     * Creates and executes a CLI command and returns the exit code;
     * echoes exceptions along the way.
     *
     * @param string $file The command file that maps to a class name.
     *
     * @return int The return code from the command.
     *
     */
    public function exec($file)
    {
        try {
            // remove the invoking-script argument
            $this->context->shiftArgv();
            // create and execute the command
            $command = $this->factory->newInstance($file);
            return (int) $command->exec();
        } catch (Exception $e) {
            $this->echoException($e);
            $code = $e->getCode();
            if (is_numeric($code) && $code == (int) $code) {
                // the code is an integer, return it as the error code
                $code = (int) $code;
            } else {
                // the code is not an integer, return 1 as the error code
                $code = 1;
            };
            return $code;
        }
    }

    /**
     *
     * Echoes an exception and all its previous exceptions.
     *
     * @param Exception $e The exception to echo.
     *
     * @return void
     *
     */
    protected function echoException(Exception $e = null)
    {
        if ($e) {
            echo $e . PHP_EOL;
            $this->echoException($e->getPrevious());
        }
    }
}

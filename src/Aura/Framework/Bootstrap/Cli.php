<?php
namespace Aura\Framework\Bootstrap;

use Aura\Cli\Context;
use Aura\Framework\Cli\Factory as CliFactory;

class Cli
{
    public function __construct(
        Context $context,
        CliFactory $factory
    ) {
        $this->context = $context;
        $this->factory = $factory;
    }
    
    /**
     * 
     * Execute bootstrap in a CLI context.
     * 
     * @param string $class The command class to instantiate and execute.
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
            return $e->getCode();
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
    protected function echoExeception(Exception $e = null)
    {
        if ($e) {
            echo $e . PHP_EOL;
            $this->echoException($e->getPrevious());
        }
    }
}

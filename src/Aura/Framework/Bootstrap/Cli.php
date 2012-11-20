<?php
namespace Aura\Framework\Bootstrap;

use Aura\Cli\Context;
use Aura\Di\ForgeInterface;

class Cli
{
    public function __construct(
        Context $context,
        ForgeInterface $forge
    ) {
        $this->context = $context;
        $this->forge = $forge;
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
    public function exec($class)
    {
        try {
            $this->context->shiftArgv();
            $command = $this->forge->newInstance($class);
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
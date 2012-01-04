<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Cli\HelloWorld;
use Aura\Framework\Cli\AbstractCommand;

/**
 * 
 * A simple CLI command to output "Hello, World!"
 * 
 * @package Aura.Framework
 * 
 */
class Command extends AbstractCommand
{
    public function action()
    {
        $this->stdio->outln("Hello World!");
    }
}

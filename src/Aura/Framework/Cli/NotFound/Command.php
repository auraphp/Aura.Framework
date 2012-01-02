<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Cli\NotFound;
use Aura\Framework\Cli\Command as CliCommand;

/**
 * 
 * Tells us the command was not found.
 * 
 * @package Aura.Framework
 * 
 */
class Command extends CliCommand
{
    public function action()
    {
        $this->stdio->errln("Command not found.");
    }
}

<?php
namespace Aura\Framework\Cli\CacheConfig;
use Aura\Framework\Cli\AbstractCommandTest;

class CommandTest extends AbstractCommandTest
{
    protected $command_class = '\Aura\Framework\Cli\CacheConfig\Command';
    
    protected function newCommand($argv = [], $system_dir = null)
    {
        $command = parent::newCommand($argv, $system_dir);
        $command->setSystem($this->system);
        return $command;
    }
}

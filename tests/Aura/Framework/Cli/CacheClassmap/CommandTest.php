<?php
namespace Aura\Framework\Cli\CacheClassmap;

use Aura\Framework\Cli\AbstractCommandTest;

class CommandTest extends AbstractCommandTest
{
    protected $command_class = '\Aura\Framework\Cli\CacheClassmap\Command';
    
    protected function newCommand($argv = [], $system_dir = null)
    {
        $command = parent::newCommand($argv, $system_dir);
        $command->setSystem($this->system);
        return $command;
    }
    
    public function testAction()
    {
        $command = $this->newCommand();
        
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}

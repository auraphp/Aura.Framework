<?php
namespace Aura\Framework\Cli;
use Aura\Cli\Command as CliCommand;
use Aura\Signal\Manager as SignalManager;
abstract class Command extends CliCommand
{
    protected $signal;
    
    public function setSignal(SignalManager $signal)
    {
        $this->signal = $signal;
        $this->signal->handler($this, 'pre_exec',    [$this, 'preExec']);
        $this->signal->handler($this, 'pre_action',  [$this, 'preAction']);
        $this->signal->handler($this, 'post_action', [$this, 'postAction']);
        $this->signal->handler($this, 'post_exec',   [$this, 'postExec']);
    }
    
    public function exec()
    {
        $this->signal->send($this, 'pre_exec', $this);
        $this->signal->send($this, 'pre_action', $this);
        $this->action();
        $this->signal->send($this, 'post_action', $this);
        $this->signal->send($this, 'post_exec', $this);
        
        // return terminal output to normal colors
        $this->stdio->out("%n");
        $this->stdio->err("%n");
    }
}
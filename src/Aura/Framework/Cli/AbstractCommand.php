<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Cli;
use Aura\Cli\AbstractCommand as AbstractCliCommand;
use Aura\Signal\Manager as SignalManager;

/**
 * 
 * Abstract class for framework commands.
 * 
 * @package Aura.Framework
 * 
 */
abstract class AbstractCommand extends AbstractCliCommand
{
    /**
     * 
     * A signal manager.
     * 
     * @var SignalManager
     * 
     */
    protected $signal;
    
    /**
     * 
     * Sets the signal manager and adds handler hooks.
     * 
     * @param SignalManager $signal The signal manager.
     * 
     * @return void
     * 
     */
    public function setSignal(SignalManager $signal)
    {
        $this->signal = $signal;
        $this->signal->handler($this, 'pre_exec',    [$this, 'preExec']);
        $this->signal->handler($this, 'pre_action',  [$this, 'preAction']);
        $this->signal->handler($this, 'post_action', [$this, 'postAction']);
        $this->signal->handler($this, 'post_exec',   [$this, 'postExec']);
    }
    
    /**
     * 
     * Executes the action while invoking signal manager hooks.
     * 
     * @return mixed
     * 
     */
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
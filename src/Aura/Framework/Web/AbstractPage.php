<?php
namespace Aura\Framework\Web;
use Aura\Framework\Inflect;
use Aura\Framework\System;
use Aura\Router\Map as RouterMap;
use Aura\Signal\Manager as SignalManager;
use Aura\View\TwoStep;
use Aura\Web\AbstractPage as WebAbstractPage;

abstract class AbstractPage extends WebAbstractPage
{
    protected $inflect;
    
    protected $router;
    
    protected $signal;
    
    protected $system;
    
    protected $view;
    
    public function setInflect(Inflect $inflect)
    {
        $this->inflect = $inflect;
    }
    
    public function setRouter(RouterMap $router)
    {
        $this->router = $router;
    }
    
    public function setSignal(SignalManager $signal)
    {
        $this->signal = $signal;
        $this->signal->handler($this, 'pre_exec',    [$this, 'preExec']);
        $this->signal->handler($this, 'pre_action',  [$this, 'preAction']);
        $this->signal->handler($this, 'post_action', [$this, 'postAction']);
        $this->signal->handler($this, 'pre_render',  [$this, 'preRender']);
        $this->signal->handler($this, 'post_render', [$this, 'postRender']);
        $this->signal->handler($this, 'post_exec',   [$this, 'postExec']);
    }
    
    public function setSystem(System $system)
    {
        $this->system = $system;
    }
    
    public function setView(TwoStep $view)
    {
        $this->view = $view;
        
        // get all included files
        $includes = array_reverse(get_included_files());
        
        // get the class hierarchy, dropping Aura.Web and Aura.Framework,
        // and adding this class itself
        $class = get_class($this);
        $stack = class_parents($class);
        array_pop($stack);
        array_pop($stack);
        array_unshift($stack, $class);
        
        // go through the hierarchy and look for each class file
        // Nb: this will not work if we concatenate all the classes into a
        // single file.
        foreach ($stack as $class) {
            $match = $this->inflect->classToFile($class);
            $len = strlen($match) * -1;
            foreach ($includes as $i => $include) {
                if (substr($include, $len) == $match) {
                    $dir = dirname($include);
                    $this->view->addInnerPath($dir . DIRECTORY_SEPARATOR . 'view');
                    $this->view->addOuterPath($dir . DIRECTORY_SEPARATOR . 'layout');
                    unset($includes[$i]);
                    break;
                }
            }
        }
    }
    
    public function exec()
    {
        // prep
        $this->signal->send($this, 'pre_exec', $this);
        
        // the action cycle
        $this->signal->send($this, 'pre_action', $this);
        $this->action();
        $this->signal->send($this, 'post_action', $this);
        
        // the render cycle
        $this->signal->send($this, 'pre_render', $this);
        $this->render();
        $this->signal->send($this, 'post_render', $this);
        
        // done
        $this->signal->send($this, 'post_exec', $this);
        return $this->response;
    }
    
    protected function render()
    {
        $this->view->setFormat($this->getFormat());
        if (! $this->response->getContent()) {
            $this->view->setInnerData((array) $this->getData());
            $this->view->setAccept($this->getContext()->getAccept());
            $this->response->setContent($this->view->render());
        }
        $this->response->setContentType($this->view->getContentType());
    }
}

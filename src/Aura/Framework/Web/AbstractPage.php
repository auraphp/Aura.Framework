<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Web;
use Aura\Framework\Inflect;
use Aura\Framework\System;
use Aura\Router\Map as RouterMap;
use Aura\Signal\Manager as SignalManager;
use Aura\View\TwoStep;
use Aura\Web\AbstractPage as WebAbstractPage;

/**
 * 
 * An abstract web page controller for the framework.
 * 
 * @package Aura.Framework
 * 
 */
abstract class AbstractPage extends WebAbstractPage
{
    /**
     * 
     * An inflection object.
     * 
     * @var Inflect
     * 
     */
    protected $inflect;
    
    /**
     * 
     * A router object.
     * 
     * @var RouterMap
     * 
     */
    protected $router;
    
    /**
     * 
     * A signal manager
     * 
     * @var SignalManager
     * 
     */
    protected $signal;
    
    /**
     * 
     * A system object.
     * 
     * @var System
     * 
     */
    protected $system;
    
    /**
     * 
     * A two-step view object.
     * 
     * @var TwoStep
     * 
     */
    protected $view;
    
    /**
     * 
     * Sets the inflection object.
     * 
     * @param Inflect $inflect The inflection object.
     * 
     * @return void
     * 
     */
    public function setInflect(Inflect $inflect)
    {
        $this->inflect = $inflect;
    }
    
    /**
     * 
     * Sets the router object.
     * 
     * @param RouterMap $router The router object.
     * 
     * @return void
     * 
     */
    public function setRouter(RouterMap $router)
    {
        $this->router = $router;
    }
    
    /**
     * 
     * Sets the signal manager and adds handlers for hooks.
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
        $this->signal->handler($this, 'pre_render',  [$this, 'preRender']);
        $this->signal->handler($this, 'post_render', [$this, 'postRender']);
        $this->signal->handler($this, 'post_exec',   [$this, 'postExec']);
    }
    
    /**
     * 
     * Sets the system object.
     * 
     * @param System $system The system object.
     * 
     * @return void
     * 
     */
    public function setSystem(System $system)
    {
        $this->system = $system;
    }
    
    /**
     * 
     * Sets the two-step view object and sets inner and outer template paths.
     * 
     * @param TwoStep $view The two-step view object.
     * 
     * @return void
     * 
     */
    public function setView(TwoStep $view)
    {
        $this->view = $view;
        
        // get all included files
        $includes = array_reverse(get_included_files());
        
        // get the class hierarchy stack
        $class = get_class($this);
        $stack = class_parents($class);
        
        // drop Aura.Web and Aura.Framework
        array_pop($stack);
        array_pop($stack);
        
        // add this class itself
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
    
    /**
     * 
     * Executes the page action, invoking hooks via the signal manager.
     * 
     * @return Aura\Web\Response
     * 
     */
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
    
    /**
     * 
     * Renders the view into the response and sets the response content-type.
     * 
     * N.b.: If the response content is already set, the view will not be
     * rendered.
     * 
     * @return void
     * 
     */
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

<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Framework
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Web\Controller;

use Aura\Framework\System;
use Aura\Router\Map as RouterMap;
use Aura\Web\Controller\AbstractPage as WebAbstractPage;

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
     * The layout to use for rendering.
     * 
     * @var mixed
     * 
     */
    protected $layout;

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
     * A system object.
     * 
     * @var System
     * 
     */
    protected $system;

    /**
     * 
     * The view to use for rendering.
     * 
     * @var mixed
     * 
     */
    protected $view;

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
     * Returns the layout to use for rendering.
     * 
     * @return mixed
     * 
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * 
     * Returns the view to use for rendering.
     * 
     * @return mixed
     * 
     */
    public function getView()
    {
        return $this->view;
    }
}
 
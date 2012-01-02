<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.View
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\View\Helper;
use Aura\View\Helper\AbstractHelper;
use Aura\Router\Map as RouterMap;

/**
 * 
 * Generates route links.
 * 
 */
class Route extends AbstractHelper
{
    /**
     * 
     * A router map object.
     * 
     * @var RouterMap
     * 
     */
    protected $router;
    
    /**
     * 
     * Constructor.
     * 
     * @param RouterMap $router The router map.
     * 
     */
    public function __construct(RouterMap $router)
    {
        $this->router = $router;
    }
    
    /**
     * 
     * Returns a route by name; optionall interpolates data into it.
     * 
     * @param string $name The route name to look up.
     * 
     * @param array $data The data to inpterolate into the URI; data keys
     * map to param tokens in the path.
     * 
     * @return string|false A URI path string if the route name is found, or
     * boolean false if not.
     * 
     */
    public function __invoke($name, array $data = [])
    {
        return $this->router->generate($name, $data);
    }
}

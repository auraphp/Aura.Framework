<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Web;
use Aura\Di\ForgeInterface as ForgeInterface;
use Aura\Framework\Exception\NoClassForController;

/**
 * 
 * A factory to create controller objects; these need not be only Page
 * controllers, but (e.g.) Resource or App controllers.
 * 
 * @package Aura.Web
 * 
 */
class Factory
{
    /**
     * 
     * An object-creation Forge.
     * 
     * @var ForgeInterface
     * 
     */
    protected $forge;
    
    /**
     * 
     * A map of controller names to controller classes.
     * 
     * @var ForgeInterface
     * 
     */
    protected $map = [];
    
    /**
     * 
     * The controller class to instantiate when no mapping is found.
     * 
     * @var ForgeInterface
     * 
     */
    protected $not_found = null;
    
    /**
     * 
     * Constructor.
     * 
     * @param Aura\Di\ForgeInterface $forge An object-creation Forge.
     * 
     * @param array $map A map of controller names to controller classes.
     * 
     * @param string $not_found The controller class to instantiate when no 
     * mapping is found.
     * 
     */
    public function __construct(
        ForgeInterface $forge,
        array $map = [],
        $not_found = null
    ) {
        $this->forge     = $forge;
        $this->map       = $map;
        $this->not_found = $not_found;
    }
    
    /**
     * 
     * Creates and returns a controller class based on a controller name.
     * 
     * @param string $name The controller name.
     * 
     * @param array $params Params to pass to the controller.
     * 
     * @return Page A controller instance.
     * 
     */
    public function newInstance($name, $params)
    {
        if (isset($this->map[$name])) {
            $class = $this->map[$name];
        } elseif ($this->not_found) {
            $class = $this->not_found;
        } else {
            throw new NoClassForController("'$name'");
        }
        
        return $this->forge->newInstance($class, ['params' => $params]);
    }
}

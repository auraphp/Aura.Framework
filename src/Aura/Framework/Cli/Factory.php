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
namespace Aura\Framework\Cli;

use Aura\Di\ForgeInterface as ForgeInterface;
use Aura\Framework\Exception\NoClassForController;

/**
 * 
 * A factory to create controller objects; these need not be only Page
 * controllers, but (e.g.) Resource or App controllers.
 * 
 * @package Aura.Framework
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
     * A map of command names to controller classes.
     * 
     * @var array
     * 
     */
    protected $map = [];

    /**
     * 
     * Constructor.
     * 
     * @param ForgeInterface $forge An object-creation Forge.
     * 
     * @param array $map A map of controller names to controller classes.
     * 
     */
    public function __construct(
        ForgeInterface $forge,
        array $map = []
    ) {
        $this->forge     = $forge;
        $this->map       = $map;
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
    public function newInstance($file)
    {
        if (! isset($this->map[$file])) {
            throw new NoClassForController($file);
        }

        $class = $this->map[$file];
        return $this->forge->newInstance($class);
    }
}

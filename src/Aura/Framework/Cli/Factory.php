<?php
/**
 *
 * This file is part of the Aura project for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Framework\Cli;

use Aura\Di\ForgeInterface as ForgeInterface;
use Aura\Framework\Exception\NoClassForController;

/**
 *
 * A factory to create command objects.
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
     * A map of command names to command classes.
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
     * Creates and returns a command class based on a controller name.
     *
     * @param string $file The command file that maps to a controller name
     *
     * @return AbstractCommand A CLI command instance.
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

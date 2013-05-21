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
namespace Aura\Framework\Test;

/**
 * 
 * A trait for assertions when testing config wiring. You should use
 * this trait in a class that extends PHPUnit_Framework_TestCase, and you
 * should call $this->loadDi() in your setup method.
 * 
 * @package Aura.Framework
 * 
 */
trait WiringAssertionsTrait
{
    /**
     * 
     * The DI container from the framework.
     * 
     * @var Aura\Di\Container
     * 
     */
    protected $di;
    
    /**
     * 
     * Loads the $di property from the global variable $AURA_FRAMEWORK_DI.
     * 
     * @return void
     * 
     */
    protected function loadDi()
    {
        $this->di = $GLOBALS['AURA_FRAMEWORK_DI'];
    }
    
    /**
     * 
     * Asserts that a DI service is an instance of a particular class. This
     * tests that the service is instantiated without wiring errors; whether
     * or not the wiring is sane is something else entirely.
     * 
     * @param string $service The service name.
     * 
     * @param string $class The class name.
     * 
     * @return void
     * 
     */
    protected function assertGet($service, $class)
    {
        $object = $this->di->get($service);
        $this->assertInstanceOf($class, $object);
        return $object;
    }
    
    /**
     * 
     * Asserts that a new instance is of a particular class. This tests that
     * the class is instantiated without wiring errors; whether or not the
     * wiring is sane is something else entirely.
     * 
     * @param string $expect Should be an instance of this class.
     * 
     * @param string $actual An alternative class to instantiate; useful for
     * mocks of abstract expected classes.
     * 
     * @return void
     * 
     */
    protected function assertNewInstance($expect, $actual = null)
    {
        if (! $actual) {
            $actual = $expect;
        }
        $object = $this->di->newInstance($actual);
        $this->assertInstanceOf($expect, $object);
        return $object;
    }
}

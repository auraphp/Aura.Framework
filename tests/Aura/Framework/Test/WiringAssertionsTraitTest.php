<?php
namespace Aura\Framework\Test;

use Aura\Di\Config;
use Aura\Di\Container;
use Aura\Di\Forge;
use Aura\Framework\Test\WiringAssertionsTrait;
use StdClass;

class WiringAssertionsTraitTest extends \PHPUnit_Framework_TestCase
{
    use WiringAssertionsTrait;
    
    public function setUp()
    {
        $GLOBALS['AURA_FRAMEWORK_DI'] = new Container(new Forge(new Config));
        $this->loadDi();
    }
    
    public function testAssertGet()
    {
        $this->di->set('service_name', new StdClass);
        $this->assertGet('service_name', 'StdClass');
    }
    
    public function testAssertNewInstance()
    {
        $this->assertNewInstance('StdClass');
    }
}

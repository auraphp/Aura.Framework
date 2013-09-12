<?php
namespace Aura\Framework\Web\Controller;

use Aura\Di\Config;
use Aura\Di\Forge;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    public function setUp()
    {
        parent::setUp();
    }

    protected function newFactory(array $map = [], $not_found = null)
    {
        return new Factory(
            new Forge(new Config),
            $map,
            $not_found
        );
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testNewInstance()
    {
        $factory = $this->newFactory([
            'mock' => 'StdClass',
        ]);
        $name = 'mock';
        $params = [];
        $controller = $factory->newInstance($name, $params);
        $this->assertInstanceOf('StdClass', $controller);
    }
    
    public function testNewInstanceNotFound()
    {
        $factory = $this->newFactory([], 'StdClass');
        $name = 'no-such-name';
        $params = [];
        $controller = $factory->newInstance($name, $params);
        $this->assertInstanceOf('StdClass', $controller);
    }
    
    public function testNewInstanceException()
    {
        $factory = $this->newFactory();
        $name = 'no-such-name';
        $params = [];
        $this->setExpectedException('Aura\Framework\Exception\NoClassForController');
        $controller = $factory->newInstance($name, $params);
    }
}

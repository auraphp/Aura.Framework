<?php
namespace Aura\Framework\Bootstrap;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    protected function setUp()
    {
        // use the real system root
        $root = null;
        $map = ['mock' => 'Aura\Framework\Mock\Bootstrap'];
        $this->factory = new Factory($root, $map);
    }

    public function testNewInstance()
    {
        $bootstrap = $this->factory->newInstance('mock', 'test', true);
        $this->assertInstanceOf('Aura\Framework\Mock\Bootstrap', $bootstrap);
    }
}

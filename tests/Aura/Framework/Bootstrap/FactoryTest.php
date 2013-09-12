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

    public function testExec()
    {
        $bootstrap = $this->factory->newInstance('mock', 'test');
        $this->assertInstanceOf('Aura\Framework\Mock\Bootstrap', $bootstrap);
    }
}

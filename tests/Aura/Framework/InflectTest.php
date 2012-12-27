<?php
namespace Aura\Framework;

/**
 * Test class for Inflect.
 */
class InflectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Inflect
     */
    protected $inflect;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->inflect = new Inflect;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    public function testCamelToDashes()
    {
        $expect = 'foo-bar-baz';
        $actual = $this->inflect->camelToDashes('FooBarBaz');
        $this->assertSame($expect, $actual);
    }

    public function testCamelToUnder()
    {
        $expect = 'Foo_Bar_Baz';
        $actual = $this->inflect->camelToUnder('FooBarBaz');
        $this->assertSame($expect, $actual);
    }

    public function testClassToFile()
    {
        // used PSR-0 spec as a base
        $list = [
            'Doctrine\Common\IsolatedClassLoader'   => 'Doctrine' . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'IsolatedClassLoader.php',
            'Symfony\Core\Request'                  => 'Symfony' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Request.php',
            'Zend'                                  => 'Zend.php',
            'Zend\Acl'                              => 'Zend' . DIRECTORY_SEPARATOR . 'Acl.php',
            'Zend\Mail\Message'                     => 'Zend' . DIRECTORY_SEPARATOR . 'Mail' . DIRECTORY_SEPARATOR . 'Message.php',
            'aura\package\ClassName'               => 'aura' . DIRECTORY_SEPARATOR . 'package' . DIRECTORY_SEPARATOR . 'ClassName.php',
            'aura\pkg_name\Class_Name'             => 'aura' . DIRECTORY_SEPARATOR . 'pkg_name' . DIRECTORY_SEPARATOR . 'Class' . DIRECTORY_SEPARATOR . 'Name.php',
        ];
        
        foreach ($list as $class => $expect) {
            $actual = $this->inflect->classToFile($class);
            $this->assertSame($expect, $actual);
        }
    }

    public function testDashesToCamel()
    {
        $expect = 'fooBarBaz';
        $actual = $this->inflect->dashesToCamel('foo-bar-baz');
        $this->assertSame($expect, $actual);
    }

    public function testDashesToStudly()
    {
        $expect = 'FooBarBaz';
        $actual = $this->inflect->dashesToStudly('foo-bar-baz');
        $this->assertSame($expect, $actual);
    }

    public function testDashesToUnder()
    {
        $expect = 'foo_bar_baz';
        $actual = $this->inflect->dashesToUnder('foo-bar-baz');
        $this->assertSame($expect, $actual);
    }
    
    public function testUnderToCamel()
    {
        $expect = 'fooBarBaz';
        $actual = $this->inflect->underToCamel('Foo_Bar_Baz');
        $this->assertSame($expect, $actual);
    }

    public function testUnderToStudly()
    {
        $expect = 'FooBarBaz';
        $actual = $this->inflect->underToStudly('Foo_Bar_Baz');
        $this->assertSame($expect, $actual);
    }

    public function testUnderToDashes()
    {
        $expect = 'foo-bar-baz';
        $actual = $this->inflect->underToDashes('foo_bar_baz');
        $this->assertSame($expect, $actual);
    }
}

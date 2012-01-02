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
            'Doctrine\Common\IsolatedClassLoader'   => 'Doctrine/Common/IsolatedClassLoader.php',
            'Symfony\Core\Request'                  => 'Symfony/Core/Request.php',
            'Zend'                                  => 'Zend.php',
            'Zend\Acl'                              => 'Zend/Acl.php',
            'Zend\Mail\Message'                     => 'Zend/Mail/Message.php',
            'aura\package\ClassName'               => 'aura/package/ClassName.php',
            'aura\pkg_name\Class_Name'             => 'aura/pkg_name/Class/Name.php',
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

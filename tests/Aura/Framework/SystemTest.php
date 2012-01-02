<?php
namespace Aura\Framework;

/**
 * Test class for System.
 */
class SystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var System
     */
    protected $system;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->system = Mock\System::newInstance();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @todo Implement testGetRootPath().
     */
    public function testGetRootPath()
    {
        $expect = dirname(dirname(__DIR__))
                . DIRECTORY_SEPARATOR . 'tmp';
        
        $actual = $this->system->getRootPath();
        $this->assertSame($expect, $actual);
        
        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
        
        $actual = $this->system->getRootPath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @todo Implement testGetPackagePath().
     */
    public function testGetPackagePath()
    {
        $expect = dirname(dirname(__DIR__))
                . DIRECTORY_SEPARATOR . 'tmp'
                . DIRECTORY_SEPARATOR . 'package';
        
        $actual = $this->system->getPackagePath();
        $this->assertSame($expect, $actual);
        
        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
                
        $actual = $this->system->getPackagePath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @todo Implement testGetTmpPath().
     */
    public function testGetTmpPath()
    {
        $expect = dirname(dirname(__DIR__))
                . DIRECTORY_SEPARATOR . 'tmp'
                . DIRECTORY_SEPARATOR . 'tmp';
        
        $actual = $this->system->getTmpPath();
        $this->assertSame($expect, $actual);
        
        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
                
        $actual = $this->system->getTmpPath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @todo Implement testGetWebPath().
     */
    public function testGetWebPath()
    {
        $expect = dirname(dirname(__DIR__))
                . DIRECTORY_SEPARATOR . 'tmp'
                . DIRECTORY_SEPARATOR . 'web';
        
        $actual = $this->system->getWebPath();
        $this->assertSame($expect, $actual);
        
        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
                
        $actual = $this->system->getWebPath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @todo Implement testGetConfigPath().
     */
    public function testGetConfigPath()
    {
        $expect = dirname(dirname(__DIR__))
                . DIRECTORY_SEPARATOR . 'tmp'
                . DIRECTORY_SEPARATOR . 'config';
        
        $actual = $this->system->getConfigPath();
        $this->assertSame($expect, $actual);
        
        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
                
        $actual = $this->system->getConfigPath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @todo Implement testGetIncludePath().
     */
    public function testGetIncludePath()
    {
        $expect = dirname(dirname(__DIR__))
                . DIRECTORY_SEPARATOR . 'tmp'
                . DIRECTORY_SEPARATOR . 'include';
        
        $actual = $this->system->getIncludePath();
        $this->assertSame($expect, $actual);
        
        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
                
        $actual = $this->system->getIncludePath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }
}

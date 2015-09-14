<?php
namespace Aura\Framework;

use Aura\Framework\VfsSystem;

class SystemTest extends \PHPUnit_Framework_TestCase
{
    protected $system;

    protected $root;
    
    public function setUp()
    {
        parent::setUp();
        $this->root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $this->system = new System($this->root);
    }

    public function testGetRootPath()
    {
        $expect = $this->root;
        $actual = $this->system->getRootPath();
        $this->assertSame($expect, $actual);
        
        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
        $actual = $this->system->getRootPath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }
    
    
    public function test__toString()
    {
        $expect = $this->root;
        $actual = (string) $this->system;
        $this->assertSame($expect, $actual);
    }
    
    public function testGetPackagePath()
    {
        $expect = $this->root . DIRECTORY_SEPARATOR . 'package';
        $actual = $this->system->getPackagePath();
        $this->assertSame($expect, $actual);
        
        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
        $actual = $this->system->getPackagePath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }
    
    public function testGetTmpPath()
    {
        $expect = $this->root . DIRECTORY_SEPARATOR . 'tmp';
        $actual = $this->system->getTmpPath();
        $this->assertSame($expect, $actual);
        
        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
        $actual = $this->system->getTmpPath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }
    
    public function testGetWebPath()
    {
        $expect = $this->root . DIRECTORY_SEPARATOR . 'web';
        $actual = $this->system->getWebPath();
        $this->assertSame($expect, $actual);
        
        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
        $actual = $this->system->getWebPath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }
    
    public function testGetConfigPath()
    {
        $expect = $this->root . DIRECTORY_SEPARATOR . 'config';
        $actual = $this->system->getConfigPath();
        $this->assertSame($expect, $actual);
        
        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
        $actual = $this->system->getConfigPath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }
    
    public function testGetIncludePath()
    {
        $expect = $this->root . DIRECTORY_SEPARATOR . 'include';
        $actual = $this->system->getIncludePath();
        $this->assertSame($expect, $actual);
        
        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
        $actual = $this->system->getIncludePath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }

    public function testGetVendorPath()
    {

        $expect = $this->root . DIRECTORY_SEPARATOR . 'vendor';
        $actual = $this->system->getVendorPath();
        $this->assertSame($expect, $actual);

        $expect .= DIRECTORY_SEPARATOR . 'foo'
                 . DIRECTORY_SEPARATOR . 'bar'
                 . DIRECTORY_SEPARATOR . 'baz';
        $actual = $this->system->getVendorPath('foo/bar/baz');
        $this->assertSame($expect, $actual);

        $expect .= DIRECTORY_SEPARATOR . 'autoload.php';
        $actual = $this->system->getVendorPath('foo/bar/baz/autoload.php');
        $this->assertSame($expect, $actual);
    }
}

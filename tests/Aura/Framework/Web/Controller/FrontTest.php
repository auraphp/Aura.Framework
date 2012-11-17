<?php
namespace Aura\Framework\Web\Controller;

use Aura\Di\Config;
use Aura\Di\Forge;
use Aura\Framework\Web\Controller\Factory;
use Aura\Http\Cookie\Collection as Cookies;
use Aura\Http\Cookie\Factory as CookieFactory;
use Aura\Http\Header\Collection as Headers;
use Aura\Http\Header\Factory as HeaderFactory;
use Aura\Http\Message\Response as HttpResponse;
use Aura\Router\Map as RouterMap;
use Aura\Router\RouteFactory;
use Aura\Router\DefinitionFactory;
use Aura\Signal\HandlerFactory;
use Aura\Signal\Manager as SignalManager;
use Aura\Signal\ResultCollection;
use Aura\Signal\ResultFactory;
use Aura\Web\Context;

class FrontTest extends \PHPUnit_Framework_TestCase
{
    protected $signal;
    
    protected $context;
    
    protected $router;
    
    protected $forge;
    
    protected $factory;
    
    protected $response;
    
    protected $front;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function newFront($path_info)
    {
        parent::setUp();
        
        $this->signal = new SignalManager(new HandlerFactory, new ResultFactory, new ResultCollection);
        
        $_SERVER['PATH_INFO'] = $path_info;
        $this->context = new Context($GLOBALS);
        
        $this->router = new RouterMap(new DefinitionFactory, new RouteFactory);
        $this->router->add(null, '/mock', [
            'values' => [
                'controller' => 'mock',
            ],
        ]);
        
        $this->forge = new Forge(new Config);
        
        $map = ['mock' => 'Aura\Framework\Mock\Page'];
        $not_found = 'Aura\Framework\Mock\NotFound';
        $this->factory = new Factory($this->forge, $map, $not_found);
        
        $this->response = new HttpResponse(new Headers(new HeaderFactory), new Cookies(new CookieFactory));
        
        return new Front(
            $this->signal,
            $this->context,
            $this->router,
            $this->factory,
            $this->response
        );
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
     * @todo Implement test__get().
     */
    public function test__get()
    {
        $front = $this->newFront('/');
        $this->assertSame($this->signal,   $front->signal);
        $this->assertSame($this->context,  $front->context);
        $this->assertSame($this->router,   $front->router);
        $this->assertSame($this->factory,  $front->factory);
        $this->assertSame($this->response, $front->response);
    }
    
    public function testExec()
    {
        $front = $this->newFront('/mock');
        $response = $front->exec();
        $this->assertInstanceOf('Aura\Http\Message\Response', $response);
        $expect = "Aura\Framework\Mock\NotFound::exec";
        $actual = $response->getContent();
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_notFound()
    {
        $front = $this->newFront('/no-such-controller');
        $response = $front->exec();
        $this->assertInstanceOf('Aura\Http\Message\Response', $response);
        $expect = "Aura\Framework\Mock\NotFound::exec";
        $actual = $response->getContent();
        $this->assertSame($expect, $actual);
    }
}

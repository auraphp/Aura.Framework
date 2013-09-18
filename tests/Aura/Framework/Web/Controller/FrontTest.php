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
use Aura\Session\Manager as SessionManager;
use Aura\Session\SegmentFactory;
use Aura\Session\CsrfTokenFactory;
use Aura\Session\Randval;
use Aura\Session\Phpfunc;

class FrontTest extends \PHPUnit_Framework_TestCase
{
    protected $signal;
    
    protected $context;
    
    protected $router;
    
    protected $forge;
    
    protected $factory;
    
    protected $response;
    
    protected $session;
    
    protected $front;
    
    protected function newFront($request_uri)
    {
        parent::setUp();
        
        $this->signal = new SignalManager(new HandlerFactory, new ResultFactory, new ResultCollection);
        
        $_SERVER['REQUEST_URI'] = $request_uri;
        $this->context = new Context($GLOBALS);
        
        $this->router = new RouterMap(new DefinitionFactory, new RouteFactory);
        $this->router->add(null, '/mock', [
            'values' => [
                'controller' => 'mock',
            ],
        ]);
        
        $this->forge = new Forge(new Config);
        
        $map = ['mock' => 'Aura\Framework\Mock\Controller'];
        $not_found = 'Aura\Framework\Mock\NotFound';
        $this->factory = new Factory($this->forge, $map, $not_found);
        
        $this->response = new HttpResponse(new Headers(new HeaderFactory), new Cookies(new CookieFactory));
        
        $this->session = new SessionManager(new SegmentFactory, new CsrfTokenFactory(new Randval(new Phpfunc)));
        
        return new Front(
            $this->signal,
            $this->context,
            $this->router,
            $this->factory,
            $this->response,
            $this->session
        );
    }
    
    public function tearDown()
    {
        parent::tearDown();
    }

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
        $expect = "Aura\Framework\Mock\Controller::exec";
        $actual = $response->getContent();
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_indexPhp()
    {
        $front = $this->newFront('/index.php/mock');
        $response = $front->exec();
        $this->assertInstanceOf('Aura\Http\Message\Response', $response);
        $expect = "Aura\Framework\Mock\Controller::exec";
        $actual = $response->getContent();
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_indexPhpEmpty()
    {
        $front = $this->newFront('/index.php');
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

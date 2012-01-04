<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Web;
use Aura\Framework\Web\Factory;
use Aura\Http\Response as HttpResponse;
use Aura\Router\Map as RouterMap;
use Aura\Signal\Manager as SignalManager;
use Aura\Web\Context;
use Aura\Web\Response as WebResponse;

/**
 * 
 * Takes an incoming web request (Context), then dispatches it, renders
 * content, and returns a response for it.
 * 
 * @package Aura.Framework
 * 
 */
class Front
{
    /**
     * 
     * The web request context.
     * 
     * @var Aura\Web\Context
     * 
     */
    protected $context;
    
    /**
     * 
     * The web reponse transfer object returned from the controller.
     * 
     * @var Aura\Web\Response
     * 
     */
    protected $transfer;
    
    /**
     * 
     * The full HTTP response created from the transfer object.
     * 
     * @var Aura\Http\Response
     * 
     */
    protected $response;
    
    protected $factory;
    
    protected $signal;
    
    protected $router;
    
    
    /**
     * 
     * Constructor.
     * 
     */
    public function __construct(
        SignalManager   $signal,
        Context         $context,
        RouterMap       $router,
        Factory         $factory,
        HttpResponse    $response
    ) {
        $this->signal   = $signal;
        $this->context  = $context;
        $this->router   = $router;
        $this->factory  = $factory;
        $this->response = $response;
    }
    
    /**
     * 
     * Magic read-only access to properties.
     * 
     * @param string $key The property to retrieve.
     * 
     * @return mixed
     * 
     */
    public function __get($key)
    {
        return $this->$key;
    }
    
    /**
     * 
     * Dispatches a Route to a web controller, renders a view into the
     * ReponseTransfer, and returns an HTTP response.
     * 
     * @return Aura\Http\Response
     * 
     * @signal pre_exec
     * 
     * @signal pre_request
     * 
     * @signal post_request
     * 
     * @signal pre_response
     * 
     * @signal post_response
     * 
     * @signal post_exec
     * 
     */
    public function exec()
    {
        // prep
        $this->signal->send($this, 'pre_exec', $this);
        
        // send request to a controller and get back a transfer object
        $this->signal->send($this, 'pre_request', $this);
        $this->request();
        $this->signal->send($this, 'post_request', $this);
        
        // convert the response transfer object to an HTTP response
        $this->signal->send($this, 'pre_response', $this);
        $this->response();
        $this->signal->send($this, 'post_response', $this);
        
        // done!
        $this->signal->send($this, 'post_exec', $this);
        return $this->response;
    }
    
    public function request()
    {
        // match to a route
        $path   = $this->context->getServer('PATH_INFO', '/');
        $server = $this->context->getServer();
        $route  = $this->router->match($path, $server);
        
        // was there a match?
        if ($route) {
            // retain info
            $controller = $route->values['controller'];
            $params     = $route->values;
        } else {
            // no match
            $controller = null;
            $params     = [];
        }
        
        // create controller
        $obj = $this->factory->newInstance($controller, $params);
        
        // execute and get back response transfer object
        $this->transfer = $obj->exec();
    }
    
    /**
     * 
     * Converts the web response transfer object into the HTTP response.
     * 
     * @return void
     * 
     */
    public function response()
    {
        $this->response->setVersion($this->transfer->getVersion());
        $this->response->setStatusCode($this->transfer->getStatusCode());
        $this->response->setStatusText($this->transfer->getStatusText());
        $this->response->headers->setAll($this->transfer->getHeaders());
        $this->response->cookies->setAll($this->transfer->getCookies());
        $this->response->setContent($this->transfer->getContent());
    }
}

<?php
/**
 *
 * This file is part of the Aura project for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Framework\Web\Controller;

use Aura\Framework\Web\Controller\Factory;
use Aura\Http\Message\Response as HttpResponse;
use Aura\Router\Map as RouterMap;
use Aura\Session\Manager as SessionManager;
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
     * @var Context
     *
     */
    protected $context;

    /**
     *
     * The web response transfer object returned from the controller.
     *
     * @var WebResponse
     *
     */
    protected $transfer;

    /**
     *
     * The full HTTP response created from the transfer object.
     *
     * @var HttpResponse
     *
     */
    protected $response;

    /**
     *
     * A controller factory.
     *
     * @var Factory
     *
     */
    protected $factory;

    /**
     *
     * A session manager.
     *
     * @var SessionManager
     *
     */
    protected $session;

    /**
     *
     * A signal manager.
     *
     * @var SignalManager
     *
     */
    protected $signal;

    /**
     *
     * A router map.
     *
     * @var RouterMap
     *
     */
    protected $router;

    /**
     *
     * Constructor.
     *
     * @param SignalManager $signal A signal manager.
     *
     * @param Context $context The web context.
     *
     * @param RouterMap $router The router map.
     *
     * @param Factory $factory A web page controller factory.
     *
     * @param HttpResponse $response The eventual HTTP response object.
     *
     * @param SessionManager $session A session manager.
     *
     */
    public function __construct(
        SignalManager   $signal,
        Context         $context,
        RouterMap       $router,
        Factory         $factory,
        HttpResponse    $response,
        SessionManager  $session
    ) {
        $this->signal   = $signal;
        $this->context  = $context;
        $this->router   = $router;
        $this->factory  = $factory;
        $this->response = $response;
        $this->session  = $session;
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
     * ResponseTransfer, and returns an HTTP response.
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
     * @return Aura\Http\Response
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

        // done! post-exec signal, commit the session, and return the response
        $this->signal->send($this, 'post_exec', $this);
        $this->session->commit();
        return $this->response;
    }

    /**
     *
     * Handle the incoming request.
     *
     * @return void
     *
     */
    public function request()
    {
        // get the path, and make allowances for "index.php" in the path
        $url  = $this->context->getServer('REQUEST_URI', '/');
        $path = parse_url($url, PHP_URL_PATH);
        $pos  = strpos($path, '/index.php');
        if ($pos !== false) {
            // read the path after /index.php
            $path = substr($path, $pos + 10);
            if (! $path) {
                $path = '/';
            }
        }

        // match to a route
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

<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Framework
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Bootstrap;

use Aura\Framework\Web\Controller\Front as FrontController;
use Aura\Http\Transport as HttpTransport;

/**
 * 
 * A bootstrapper for web apps.
 * 
 * @package Aura.Framework
 * 
 */
class Web
{
    /**
     * 
     * The front controller.
     * 
     * @var FrontController
     * 
     */
    protected $front_controller;

    /**
     * 
     * An HTTP transport.
     * 
     * @var HttpTransport
     * 
     */
    protected $http_transport;

    /**
     * 
     * Constructor.
     * 
     * @param FrontController $front_controller The front controller.
     * 
     * @var HttpTransport $http_transport An HTTP transport.
     * 
     */
    public function __construct(
        FrontController $front_controller,
        HttpTransport $http_transport
    ) {
        $this->front_controller = $front_controller;
        $this->http_transport = $http_transport;
    }

    /**
     * 
     * Executes the front controller, gets back an HTTP response, and 
     * sends it via the HTTP transport; catches and echoes exceptions.
     * 
     * @return void
     * 
     */
    public function exec()
    {
        try {
            $http_response = $this->front_controller->exec();
            return $this->http_transport->sendResponse($http_response);
        } catch (Exception $e) {
            $this->echoException($e);
            exit(1);
        }
    }

    /**
     * 
     * Echoes an exception and all its previous exceptions.
     * 
     * @param Exception $e The exception to echo.
     * 
     * @return void
     * 
     */
    protected function echoException(Exception $e = null)
    {
        if ($e) {
            echo $e . PHP_EOL;
            $this->echoException($e->getPrevious());
        }
    }
}

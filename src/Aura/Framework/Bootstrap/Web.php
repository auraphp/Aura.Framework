<?php
namespace Aura\Framework\Bootstrap;

use Aura\Framework\Web\Controller\Front as FrontController;
use Aura\Http\Transport as HttpTransport;

class Web
{
    protected $front_controller;

    protected $http_transport;

    public function __construct(
        FrontController $front_controller,
        HttpTransport $http_transport
    ) {
        $this->front_controller = $front_controller;
        $this->http_transport = $http_transport;
    }

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

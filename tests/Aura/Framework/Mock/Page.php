<?php
namespace Aura\Framework\Mock;

use Aura\Web\Response;
use Aura\Web\Controller\ControllerInterface;

class Page implements ControllerInterface
{
    
    public function __construct()
    {
    }
    
    /**
     * 
     * Returns the Accept object.
     * 
     * @return Accept
     * 
     */
    public function getAccept()
    {
        
    }
    
    /**
     * 
     * Returns the Context object.
     * 
     * @return Context
     * 
     */
    public function getContext()
    {
        
    }

    /**
     * 
     * Returns the data collection object.
     * 
     * @return object
     * 
     */
    public function getData()
    {
        
    }

    /**
     * 
     * Returns the params.
     * 
     * @return array
     * 
     */
    public function getParams()
    {
        
    }

    /**
     * 
     * Returns the Response object.
     * 
     * @return Response
     * 
     */
    public function getResponse()
    {
        
    }

    /**
     * 
     * Returns the SignalInterface object.
     * 
     * @return SignalInterface
     * 
     */
    public function getSignal()
    {
        
    }

    /**
     * 
     * Executes the controller.
     * 
     * @return Response
     * 
     */
    public function exec()
    {
        $response = new Response;
        $response->setContent(__METHOD__);
        return $response;
    }
}

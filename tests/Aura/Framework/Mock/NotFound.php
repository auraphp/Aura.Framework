<?php
namespace Aura\Framework\Mock;
use Aura\Web\Response;
class NotFound {
    
    public function __construct()
    {
    }
    
    public function exec()
    {
        $response = new Response;
        $response->setContent(__METHOD__);
        return $response;
    }
}

<?php
namespace Aura\Framework\Mock;
use Aura\Web\Response;
class Page {
    
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

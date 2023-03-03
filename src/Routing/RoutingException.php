<?php

namespace App\Routing;

use App\Model\Response;
use Exception;

class RoutingException extends Exception
{
    private $response;

    public function __construct($message = "")
    {
        $this->response=new Response();
        $this->response->setHttpStatusCode(404);
        $this->response->setSuccess(false);
        $this->response->addMessage($message);
        $this->response->send();
    }
}
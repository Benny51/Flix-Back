<?php

namespace App\Controller;

use App\Model\Model;
use App\Model\Response;
use PDOStatement;

class Controller
{
    /***
     * @var Response $response
     */
    protected $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    /**
     * Méthode qui renvoie une erreur si la request méthode envoyée au serveur ne correspond pas à celle de la méthode
     * */
    public function notAllowed()
    {
        $this->response->setHttpStatusCode(405);
        $this->response->setSuccess(false);
        $this->response->addMessage("Méthode de requête non permise");
        $this->response->send();
        exit;
    }
}
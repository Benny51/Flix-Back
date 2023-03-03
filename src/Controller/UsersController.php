<?php

namespace App\Controller;

use App\Model\Response;
use App\Model\Users;
use PDO;
use PDOStatement;

class UsersController extends Controller
{
    private $tableName = "user";

    /***
     * @var Users $user
     */
    private $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new Users();
    }
    
    public function getAll()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {

            $pdoStatement = $this->user->getAll();

            //S'il n'y a pas de donnée dans la table message d'erreur
            if($pdoStatement->rowCount() === 0)
            {
                $this->response->setHttpStatusCode(404);
                $this->response->setSuccess(false);
                $this->response->addMessage("La table $this->tableName est vide");
                $this->response->send();
                exit;
            }

            $this->response->setHttpStatusCode(200);
            $this->response->setSuccess(true);
            $this->response->setData($pdoStatement->fetchAll(PDO::FETCH_ASSOC));
            $this->response->send();

            exit;
        } else {
            $this->notAllowed();
        }
    }

    public function create()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {   

            $pdoStatement = $this->user->create();

            if($pdoStatement === null)
            {
                $this->response->setHttpStatusCode(404);
                $this->response->addMessage("Veuillez envoyer des données, aucun POST n'a été reçu");
                $this->response->setSuccess(false);
                $this->response->send();
                exit;
            }

            if($this->user->duplicatataUsername($this->response))
            {
                $this->response->setHttpStatusCode(409);
                $this->response->setSuccess(false);
                $this->response->send();
                exit;
            }

            $pdoStatement->execute();
        
            $this->response->setHttpStatusCode(200);
            $this->response->setSuccess(true);
            $this->response->addMessage("Ajouté avec succès");
            //Retourner le dernier élément créer
            $this->response->setData($this->user->getById((int)$this->user->getLastId())->fetch(PDO::FETCH_ASSOC));
            $this->response->send();
            exit;
        }

    }

    public function delete($id)
    {
        if($_SERVER['REQUEST_METHOD'] === 'DELETE')
        {
            //Je regarde si la clé primaire possède posséde des nombres et des chaines de caractères
            if(preg_match('/[A-Za-z]/', $id) )
            {
                $this->response->setHttpStatusCode(409);
                $this->response->setSuccess(false);
                $this->response->addMessage("La clé primaire n'est que numérique -> $id");
                $this->response->send();
                exit;
            }

            $isIdExist = $this->user->getById($id);
            //Si l'id n'existe pas message d'erreur

            if($isIdExist->rowCount()===0)
            {
                $this->response->setHttpStatusCode(404);
                $this->response->setSuccess(false);
                $this->response->addMessage("L'id $id de la table $this->tableName que vous voulez supprimer n'existe pas ou a déjà été supprimé");
                $this->response->send();
                exit;
            }

            //Sinon on le supprime
            $req = $this->user->delete($id);
            $req->execute();
            var_dump($req->errorInfo());
            $this->response->setHttpStatusCode(200);
            $this->response->setSuccess(true);
            $this->response->addMessage("L' id $id de la table $this->tableName a été supprimée avec succès");
            $this->response->send();
        }
        else {
            $this->notAllowed();
        }
    }


    public function getById($id)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {

            //Je regarde si la clé primaire possède posséde des nombres et des chaines de caractères
            if(preg_match('/[A-Za-z]/', $id))
            {
                $this->response->setHttpStatusCode(409);
                $this->response->setSuccess(false);
                $this->response->addMessage("Clé primaire n'est que numérique-> $id");
                $this->response->send();
                exit;
            }

            $pdoStatement = $this->user->getById($id);
            //Vérification que l'id existe bien
            if($pdoStatement->rowCount() === 0)
            {
                $this->response->setHttpStatusCode(404);
                $this->response->setSuccess(false);

                if(!preg_match('/[A-Za-z]/', $id) && preg_match('/[0-9]/', $id))
                    $this->response->addMessage("L'id $id de la table ". $this->tableName." n'existe pas");

                $this->response->send();
                exit;
            }
            //Si l'id existe l'affiche dans les data
            $findElemById = $pdoStatement->fetch(PDO::FETCH_ASSOC);
            $this->response->setHttpStatusCode(200);
            $this->response->setSuccess(true);
            $this->response->toCache(false);
            $this->response->setData($findElemById);
            $this->response->send();
            exit;

        } else {
            $this->notAllowed();
        }
    }




}
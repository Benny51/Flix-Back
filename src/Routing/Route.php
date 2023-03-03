<?php

namespace App\Routing;

class Route
{
    /**
     * @var string $path : Chemin de la route entrée par l'utilisateur, modifié par la méthode check_db pour pouvoir distinguer les db
     */
    private $path;
    /***
     * @var Closure | string $callable : fonction (controller ou fonction anonyme) appelée en fonction de la route
     */
    private $callable;
    /***
     * @var array
     */
    private $matches;
    /***
     * @var array
     */
    private $params;

    private $paramCount;

    public function __construct($path, $callable){

        $this->path = trim($path, '/');
        $this->callable = $callable;
        $this->params = [];
        $this->matches = [];
    }

    /***
     * @param $url
     * @return bool
     */
    public function match($url){
        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
        $regex = "#^$path$#i";

        if(!preg_match($regex, $url, $matches)){
            return false;
        }

        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    /***
     * @param $match :
     * @return string
     */
    private function paramMatch($match){

        if(isset($this->params[$match[1]])){
            return '(' . $this->params[$match[1]] . ')';
        }

        return '([^/]+)';
    }

    /***
     * Cette fonction va appeler le controller si la closure rentrée par l'utilisateur est sous forme de string
     * La closure ressemble à cela #method
     * Dans la méthode, on adapte la closure pour récupérer le nom de la table sql pour pouvoir interagir avec le controller et le modèle
     * Si la méthode n'est pas sous forme de string, c'est qu'il s'agit d'une fonction anonyme
     */
    public function call(){

        //Si c'est un appel au controller
        if(is_string($this->callable))
        {
            $params = explode("#",$this->callable);
            //Stocker le chemin et le nom du controller
            $controller = "App\\Controller\\" . $params[0] . "Controller";
            $controller = new $controller();
            return call_user_func_array([$controller,$params[1]],$this->matches);
        }
        //S'il s'agit d'une fonction anonyme
        return call_user_func_array($this->callable,$this->matches);
    }

    /***
     * Méthode qui permet de paramétrer un paramètre
     * Méthode qui peut être enchainée s'il y a plusieurs paramètres
     * @param $name_params : Paramètre entré par l'utilisateur
     * @param $regex : expression régulière que l'on utilise pour pouvoir effectuer de la sécurité sur les paramètres
     * @return Route : retourne la route pour pouvoir enchainer les méthodes
     */
    public function with($name_params, $regex)
    {
        //Supprimer les () --> (?: on ne capture pas les ()
        //Stocker le paramètres avec son expression régulière
        $this->params[$name_params] = str_replace('(','(?:',$regex);
        $this->paramCount++;
        return $this; // fluent pour enchainer les arguments
    }

}
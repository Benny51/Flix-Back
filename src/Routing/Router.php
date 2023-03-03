<?php

namespace App\Routing;

class Router
{
    /***
     * @var string $url : url tapé par l'utilisateur dans la barre de recherche
     */
    private $url;
    /***
     * Variable qui stock toutes les routes enregistrées par l'utilisateur (GET, POST, DELETE, PUT)
     * @var Route []
     */
    private $routes;
    /***
     * @var $_instance Router : Instance de router, si l'instance existe elle est juste renvoyée
     */
    private static $_instance;

    /***
     * @param $url : url rentré par l'utilisateur
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->routes = [];
    }

    /***
     * @param $url : Url qui permet de créer le router (Url de recherche pour créer les routes)
     * @return Router : retourne une instance de router s'il existe sinon il crée l'instance puis la retourne
     */
    public static function getInstance($url)
    {
        if(is_null(self::$_instance)) self::$_instance = new Router($url);
        return self::$_instance;
    }

    /**
     * @param $path : Chemin de la route
     * @param string|Closure $callable : Fonction appelée en fonction de la route
     * @param $method --> Request method (GET,POST,PUT,DELETE)
     */
    public function map($path, $callable,$method){
        $path = trim($path, '/');
        $route = new Route($path,$callable);
        $this->routes[$method][] = $route;
        return $route;
    }

    public function post($path, $callable){
        return $this->map($path, $callable,'POST');
    }

    public function get($path, $callable){
        return $this->map($path, $callable,'GET');
    }

    public function patch($path,$callable)
    {
        return $this->map($path,$callable,"PATCH");
    }

    public function put($path, $callable)
    {
        return $this->map($path,$callable,"PUT");
    }

    public function delete($path, $callable)
    {
        return $this->map($path,$callable,"DELETE");
    }
//
    /**
     * Méthode qui lance l'éxécution des routes : Vérifie si la méthode Http existe
     * Vérifie l'url entré si cela match avec une route sinon la route n'existe pas
     * Si elle existe elle appelle le controller ou la fonction anonyme
     * Si elle n'existe pas elle renverra une RoutingException
     * @throws RoutingException
     */
    public function run(){

        if(!isset($this->routes[$_SERVER['REQUEST_METHOD']])){
            throw new RoutingException("REQUEST_METHOD n'existe pas");
        }

        /***
         * @var $route Route
         */
        foreach($this->routes['POST'] as $route){
            if($route->match($this->url)){
                return $route->call();
            }
        }

        /***
         * @var $route Route
         */
        foreach($this->routes['GET'] as $route){
            if($route->match($this->url)){
                return $route->call();
            }
        }

        if(isset($this->routes['PUT']))
        {
            /***
             * @var $route Route
             */
            foreach($this->routes['PUT'] as $route){
                if($route->match($this->url)){
                    return $route->call();
                }
            }
        }

        /***
         * @var $route Route
         */
        foreach($this->routes['PATCH'] as $route){
            if($route->match($this->url)){
                return $route->call();
            }
        }

        /***
         * @var $route Route
         */
        foreach($this->routes['DELETE'] as $route){
            if($route->match($this->url)){
                return $route->call();
            }
        }

        throw new RoutingException("La route n'existe pas ou la request method n'est pas correcte");
    }


////    /***
////     * Méthode qui vérifie dans la db si la table sql existe
////     * @param $table_name : nom de la table à vérifier
////     * @return bool : return true si la table sql existe
////     * @throws RoutingException
////     */
////    private function isTableExist($table_name,$dbname)
////    {
////        $query = " select * from information_schema.TABLES where TABLE_NAME =  '$table_name'  and  TABLE_SCHEMA = '$dbname'  LIMIT 1";
////        $pdoStatement = $this->db->getBddConnect()->prepare($query);
////        $pdoStatement->execute();
////
////        if($pdoStatement->rowCount() ===1)
////        {
////            return true;
////        }
////
////        throw new RoutingException("La table n'existe pas");
////    }
//
////    /***
////     * @param $tableFromUrl : Il s'agit de la table sql qui va être vérifiée pour créer la route
////     * @param $method : request method de la route
////     * @param $path : Path de la route qui sera changé dans cette fonction pour qu'il se distincte en fonction des tables
////     * @param $callable : Fonction qui est lancée par la route
////     * @return Route
////     */
////    private function checkDb($tableFromUrl,$method,$path,$callable)
////    {
////        if($this->dbName !== null)
////        {
////            try {
////                $query = "show databases like '$this->dbName'";
////                $pdoStatement = $this->db->getBddConnect()->prepare($query);
////                $pdoStatement->execute();
////                if($pdoStatement->rowCount() === 0 )
////                {
////                    throw new RoutingException("La base de donnée $this->dbName n'existe pas");
////                }
////                if ($this->isTableExist($tableFromUrl, $this->dbName)) {
////                    $this->db->getBddConnect()->exec("use " . $this->dbName);
////                }
////            } catch (RoutingException $e) {
////                exit;
////            }
////        }
////        else{
////            try {
////                if ($this->isTableExist($tableFromUrl, "0_fid_basecentrale")) {
////                    $this->db->getBddConnect()->exec("use 0_fid_basecentrale");
////                }
////            } catch (RoutingException $e) {
////                exit;
////            }
////        }
////
////        $path = $tableFromUrl."/".$path;
////
////        $route = new Route($path,$callable,$this->db);
////        $this->routes[$method][] = $route;
////        return $route;
////    }
//
////    /**
////     * @return string
////     */
////    public function getUrl()
////    {
////        return $this->url;
////    }
////
////    /**
////     * @return mixed|string|null
////     */
////    public function getDbName()
////    {
////        return $this->dbName;
////    }
////
////    public function getCountRoutes($key = null)
////    {
////        if($key === null)
////        {
////            return count($this->routes);
////        }
////        return count($this->routes[$key]);
////    }
//
//


}
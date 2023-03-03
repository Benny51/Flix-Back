<?php

namespace App\Model;

use App\Config\Database;
use PDO;
use PDOStatement;

class Users
{

    private $table_name = "user";
    private $db;
    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAll()
    {
        $sqlQuery = "SELECT * FROM $this->table_name";
        $request = $this->db->prepare($sqlQuery);
        $request->execute();
        return $request;
    }

    public function getByUsername($username):PDOStatement
    {

        $sqlQuery = "SELECT * FROM $this->table_name where username = :username LIMIT 0,1";
        $pdoStatement = $this->db->prepare($sqlQuery);
        $pdoStatement->bindParam(":username",$username);
        $pdoStatement->execute();
        return $pdoStatement;
    }

    public function getById($id):PDOStatement
    {
        $sqlQuery = "SELECT * FROM $this->table_name where id_user =:id LIMIT 0,1";
        $pdoStatement = $this->db->prepare($sqlQuery);
        $pdoStatement->bindParam(":id",$id);
        $pdoStatement->execute();
        return $pdoStatement;
    }

    public function getLastId()
    {
        return $this->db->lastInsertId();
    }

    public function create()
    {

        // Rien n'a été envoyé
        if($_POST === [])
        {
            return null;
        }

        $sqlQuery = "INSERT into $this->table_name (email,username,password_) values(:email,:username,:password_)";

        $pdoStatement = $this->db->prepare($sqlQuery);


        $pdoStatement->bindValue("email",$_POST["email"]);
        $pdoStatement->bindValue("username",$_POST["username"]);
        $pdoStatement->bindValue("password_",$_POST["password_"]);

        return $pdoStatement;

    }

    public function update($id)
    {

    }

    public function delete($id)
    {
        $sqlQuery = "DELETE from $this->table_name where id_user = :id";
        $pdoStatement = $this->db->prepare($sqlQuery);
        $pdoStatement->bindParam(":id",$id);

        return $pdoStatement;
    }
    // public function update($id)
    // {
    //     //Je reçois toutes mes données
    //     $this->patch = json_decode(file_get_contents('php://input'),true);
    //     $date = date("Y-m-d H:i:s");
    //     $this->patch["Updated_at"] = $date;

    //     $updateStr = "";
    //     $this->columns = "";
    //     $this->params = "";

    //     foreach ($this->patch as $key => $patch)
    //     {
    //         if($updateStr === "")
    //         {
    //             $updateStr = $key . " = :". $key;
    //             $this->columns .= $key;
    //             $this->params .=':'.$key;
    //         }else{
    //             $updateStr .= ",".$key . " = :". $key;
    //             $this->columns .=','.$key;
    //             $this->params  .=',:'.$key;
    //         }
    //     }


    //     $primaryKey = $this->getPrimaryKey();
    //     $sqlUpdateQuery  = "UPDATE $this->tableName set $updateStr where $primaryKey = $id";
    //     $pdoStatement = $this->db->prepare($sqlUpdateQuery);

    //     $params = explode(",",$this->params);

    //     foreach ($params as $param){
    //         $param = substr($param,1);
    //         if(isset($this->patch[$param]))
    //         {
    //             $pdoStatement->bindValue(":".$param,$this->patch[$param]);
    //         }
    //     }


    //     return $pdoStatement;
    // }

    /***
     * @param $response
     * @return bool
     */
    public function duplicatataUsername($response):bool
    {

        $isUsernameExist = $this->getByUsername($_POST['Username']);
        //Gestion doublon Username
        if($isUsernameExist->rowCount() === 1)
        {
            $response->addMessage("Duplicata du champ '". $_POST['username']);
            return true;
        }

        return false;
    }

}
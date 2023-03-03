<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{

    private static $dbHost = 'localhost';
    private static $dbName = "flix";
    private static $dbUser = "root";
    private static $dbUserPassword = "";
    private static $connectToDB = null;


    public static function connect()
    {
        try {

            self::$connectToDB = new PDO("mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName, self::$dbUser, self::$dbUserPassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

        } catch (PDOException $e) {

            die($e->getmessage());
        }

        return self::$connectToDB;
    }





}
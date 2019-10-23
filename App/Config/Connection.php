<?php

namespace App\Config;

use App\Config\Config;
use PDO;

class Connection 
{

    private static $conn;

    public static function getConnection ()
    {

        $conn = new PDO("mysql:host=" . Config::HOST_NAME . ";dbname=" . Config::DBNAME,
        Config::LOGINDB,
        Config::PASSDB);

        try {
            
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;

        } catch (PDOException $th) {

            throw new \Exception("Erro de conexão no banco de dados");

        }

    }

}



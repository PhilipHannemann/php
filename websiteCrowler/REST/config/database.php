<?php
class Database {

    private $host = "localhost";
    private $username = "philip";
    private $password = "datenbank";
    private $database = "philipsprojekt";

    public $connection;

    /*
     Verbindung zu der Datenbank herstellen und die Verbindung zur Verfügung stellen
     */
    public function getConnection(){

        $this->connection = null;

        try{
            $this->connection = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->username, $this->password);
            $this->connection->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection Error: " . $exception->getMessage();
        }

        return $this->connection;
    }
}

?>
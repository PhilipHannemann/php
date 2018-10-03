<?php
class Restaurant{
 
    // database connection and table name
    private $conn;
    private $tableName = "Restaurant_tbl";
 
    // object properties
    public $rest_id;
    public $adresse;
    public $rest_name;
    public $webseite;
 
    // Konstruktor um mit der DB zu verbinden
    public function __construct($db){
        $this->conn = $db;
    }


    // search products
    public function search($keywords){
  
        // select all query
        $query = "SELECT * FROM " . $this->tableName . "
                WHERE
                    adresse LIKE ? OR rest_name LIKE ? OR webseite LIKE ?
                ORDER BY
                    rest_id ASC";
     
        // prepare query statement
        $stmt = $this->conn->prepare($query);
     
        // sanitize
        $keywords=htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";
     
        // bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
     
        // execute query
        $stmt->execute();
     
        return $stmt;
    }
    
}

?>
<?php
class Speisen{
 
    // database connection and table name
    private $conn;
    private $tableName = "Speisen_tbl";
 
    // object properties
    public $speise_id;
    public $speise_name;
    public $zutaten;
    public $preis;
    public $kategorie;
    public $eintragDatum;
    public $restaurant;

 
    // Konstruktor um mit der DB zu verbinden
    public function __construct($db){
        $this->conn = $db;
    }



    // search products
    public function search($keywords){
  
        // select all query
        $query = "SELECT * FROM " . $this->tableName . "
                WHERE
                    speise_name LIKE ? OR zutaten LIKE ? OR preis LIKE ? OR kategorie LIKE ?
                ORDER BY
                    kategorie, speise_name ASC";
     
        // prepare query statement
        $stmt = $this->conn->prepare($query);
     
        // sanitize
        $keywords=htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";
     
        // bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->bindParam(4, $keywords);
     
        // execute query
        $stmt->execute();
     
        return $stmt;
    }
    
}

?>
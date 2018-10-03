<?php
class Pizza{
 
    // database connection and table name
    private $conn;
    private $tableName = "Pizza_tbl";
 
    // object properties
    public $pizza_id;
    public $pizza_name;
    public $zutaten;
    public $groesse_26;
    public $groesse_30;
    public $groesse_36;
    public $groesse_familie;
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
                    pizza_name LIKE ? OR zutaten LIKE ? OR groesse_26 LIKE ? OR groesse_30 LIKE ? OR groesse_36 LIKE ? OR groesse_familie LIKE ?
                ORDER BY
                    pizza_name ASC";
     
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
        $stmt->bindParam(5, $keywords);
        $stmt->bindParam(6, $keywords);
     
        // execute query
        $stmt->execute();
     
        return $stmt;
    }
    
}

?>
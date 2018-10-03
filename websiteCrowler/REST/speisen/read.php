<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/speisen.php';
 
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$meals = new Speisen($db);

// get keywords
$keywords=isset($_GET["s"]) ? $_GET["s"] : "";
 
// query products
$stmt = $meals->search($keywords);
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
    // products array
    $menu=array();
    $menu["Speisen"]=array();
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        // Werte extrahieren
        extract($row);
 
        $meal=array(
            "speise_id" => $speise_id,
            "speise_name" => $speise_name,
            "zutaten" => html_entity_decode($zutaten),
            "preis" => number_format($preis, 2, '.', ''),
            "kategorie" => html_entity_decode($kategorie),
            "eintragDatum" => $eintragDatum,
            "restaurant" => $restaurant,

        );
 
        array_push($menu["Speisen"], $meal);
    }
 
    echo json_encode($menu);
}
 
else{
    echo json_encode(
        array("message" => "Keine Speisen gelistet")
    );
}
?>
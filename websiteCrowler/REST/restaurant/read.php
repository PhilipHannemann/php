<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/restaurant.php';
 
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$restaurant = new Restaurant($db);
 
// get keywords
$keywords=isset($_GET["s"]) ? $_GET["s"] : "";
 
// query products
$stmt = $restaurant->search($keywords);
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
 
    // products array
    $restaurants=array();
    $restaurants["Restaurant"]=array();
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        // Werte extrahieren
        extract($row);
 
        $restaurantItem=array(
            "rest_id" => $rest_id,
            "adresse" => $adresse,
            "rest_name" => html_entity_decode($rest_name),
            "webseite" => $webseite
        );
 
        array_push($restaurants["Restaurant"], $restaurantItem);
    }
 
    echo json_encode($restaurants);
}
 
else{
    echo json_encode(
        array("message" => "Keine Restaurants gelistet")
    );
}
?>
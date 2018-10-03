<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/pizza.php';
 
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$pizzen = new Pizza($db);

// get keywords
$keywords=isset($_GET["s"]) ? $_GET["s"] : "";
 
// query products
$stmt = $pizzen->search($keywords);
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
    // products array
    $pizza=array();
    $pizza["Pizzen"]=array();
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        // Werte extrahieren
        extract($row);
        $toppings = explode(",", $zutaten);
        $top = "";

        foreach ($toppings as $topping) {
            $topping = trim($topping);

            $top = ($top=="") ? $topping : $top.", ".$topping;
        }

        $pizzaItem=array(
            "pizza_id" => $pizza_id,
            "pizza_name" => html_entity_decode($pizza_name),
            "zutaten" => html_entity_decode($top),
            "groesse_26" => number_format($groesse_26, 2, '.', ''),
            "groesse_30" => number_format($groesse_30, 2, '.', ''),
            "groesse_36" => number_format($groesse_36, 2, '.', ''),
            "groesse_familie" => number_format($groesse_familie, 2, '.', ''),
            "eintragDatum" => $eintragDatum,
            "restaurant" => $restaurant,

        );
 
        array_push($pizza["Pizzen"], $pizzaItem);
    }
 
    echo json_encode($pizza);
}
 
else{
    echo json_encode(
        array("message" => "Keine Pizzen gelistet")
    );
}
?>
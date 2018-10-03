<?php 
namespace Himalaya;

    function cmp($a, $b) {
        if ($a["kategorie"] == $b["kategorie"]) {

            if ($a["name"] == $b["name"]) {
                
                if ($a["größe"] == $b["größe"]) {

                    if ($a["p1"] == $b["p1"]) {

                        if ($a["inhalt"] == $b["inhalt"]) {
                            return 0;
                        }
                        return ($a["inhalt"] < $b["inhalt"]) ? -1 : 1;
                    }
                    return ($a["p1"] < $b["p1"]) ? -1 : 1;
                }
                return ($a["größe"] < $b["größe"]) ? -1 : 1;
            }
            return ($a["name"] < $b["name"]) ? -1 : 1;
        }
        return ($a["kategorie"] < $b["kategorie"]) ? -1 : 1;
    }


    function sortArray($array){
        // Vergleichsfunktion
        
        uasort($array, 'cmp');


        return $array;
    }

    function makeArrayKonfirmation($array){
        $newArray = [];
        $temp = array('name' => "",
                    'inhalt' => "",
                    'p1' => "",
                    'p2' => "",
                    'p3' => "",
                    'p4' => "",
                    'kategorie' => ""
        );
        foreach ($array as $value) {
            $count = count($newArray);
            if ($count !=0 && $newArray[$count-1]["name"] == $value["name"]) {
                
                if($value["größe"] == "26"){
                    $newArray[$count-1]["p1"] = $value["p1"];
                }elseif ($value["größe"] == "30") {
                    $newArray[$count-1]["p2"] = $value["p1"];
                }elseif ($value["größe"] == "36") {
                    $newArray[$count-1]["p3"] = $value["p1"];
                }elseif ($value["größe"] == "34x46") {
                    $newArray[$count-1]["p4"] = $value["p1"];
                }

            }else{
                $newElem = $temp;
                $newElem["name"] = $value["name"];
                $newElem["kategorie"] = $value["kategorie"];
                $newElem["inhalt"] = $value["inhalt"];

                if($value["größe"] == "26"){
                    $newElem["p1"] = $value["p1"];
                }elseif ($value["größe"] == "30") {
                    $newElem["p2"] = $value["p1"];
                }elseif ($value["größe"] == "36") {
                    $newElem["p3"] = $value["p1"];
                }elseif ($value["größe"] == "34x46") {
                    $newElem["p4"] = $value["p1"];
                }elseif ($value["größe"] == "PP") {
                    $newElem["p4"] = $value["p1"];
                }

                array_push($newArray, $newElem);
            }

        }


        return $newArray;
    }

    function makeArrayKonfirmationMenu($array){
        $result = [];

        $max = count($array);

        for ($i=0; $i < $max; $i++) {
            $value = $array[$i];
            $newElement = array(
            "name" => $value["name"],
            "inhalt" => $value["inhalt"],
            "preis" => $value["p1"],
            "kategorie" => $value["kategorie"]
            );

            array_push($result, $newElement);
        }
                
        return $result;

    }



    function getHimalaya(){

        include("himalayaMenu.php");
        include("adressHimalaya.php");

        $menu = \himalayaMenu\getHimalayaMenu();
        $meals = sortArray($menu[0]);
        $meals = makeArrayKonfirmationMenu($meals);
        
        $pizza = sortArray($menu[1]);
        $pizza = makeArrayKonfirmation($pizza);

        //print_r($meals);
        //print_r($pizza);

        $adress = \himalayaAdress\getHimalayaAdress();

        //print_r($adress);
        return [$pizza, $meals, $adress];
  
    }

    //getHimalaya();
?>
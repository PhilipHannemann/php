<?php 
namespace Bologna;

    function cmp($a, $b) {
        if ($a["kategorie"] == $b["kategorie"]) {

            if ($a["name"] == $b["name"]) {
                
                if ($a["p1"] == $b["p1"]) {

                    if ($a["p2"] == $b["p2"]) {

                        if ($a["inhalt"] == $b["inhalt"]) {
                            return 0;
                        }
                        return ($a["inhalt"] < $b["inhalt"]) ? -1 : 1;
                    }
                    return ($a["p2"] < $b["p2"]) ? -1 : 1;
                }
                return ($a["p1"] < $b["p1"]) ? -1 : 1;
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

    function changeTableForEquation($array){
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



    function getBologna(){

        include("adressBologna.php");
        include("bolognaMenu.php");

        $menu = \menuBologna\getBolognaMenu();
        $meals = sortArray($menu[0]);
        $meals = changeTableForEquation($meals);
        $pizza = sortArray($menu[1]);
        
        //print_r($meals);
        //print_r($pizza);

        $adress = \adressBologna\getAdressBologna();

        //print_r($adress);

        return [$pizza, $meals, $adress];
    }


?>
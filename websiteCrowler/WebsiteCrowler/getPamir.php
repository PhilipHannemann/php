<?php 
namespace Pamir;

    function cmp($a, $b) {
        if ($a["kategorie"] == $b["kategorie"]) {

            if ($a["name"] == $b["name"]) {
                
                if ($a["preis"] == $b["preis"]) {
                    
                    if ($a["inhalt"] == $b["inhalt"]) {
                        return 0;
                    }
                    return ($a["inhalt"] < $b["inhalt"]) ? -1 : 1;
                }
                return ($a["preis"] < $b["preis"]) ? -1 : 1;
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



    function getPamir(){

        include("adressPamir.php");
        include("pamirMenu.php");
        include("pamirPizza.php");
        include("pamirNudeln.php");


        $pizza = \pamirPizza\getPizza();
        $pizza = sortArray($pizza);

        $menu = \pamirMenu\getPamirMenu();
        $N_V = \pamirN_V\getNudelnVorspeisen();
        $meals = array_merge($menu, $N_V);

        $meals = sortArray($meals);


        $adress = \pamirAdress\getAdressPamir();

        return [$pizza, $meals, $adress];
  
    }
    
?>
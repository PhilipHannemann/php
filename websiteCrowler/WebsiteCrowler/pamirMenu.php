<?php 
namespace pamirMenu;

    function HomepageLaden($url, $postdata) 
        { 
        $agent = "Meine Browserkennung v1.0 :)"; 
        $header[] = "Accept: text/vnd.wap.wml,*.*"; 
        $ch = curl_init($url); 

        if ($ch) 
            { 
            curl_setopt($ch,    CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch,    CURLOPT_USERAGENT, $agent); 
            curl_setopt($ch,    CURLOPT_HTTPHEADER, $header); 
            curl_setopt($ch,    CURLOPT_FOLLOWLOCATION, 1); 

            if (isset($postdata)) 
                { 
                curl_setopt($ch,    CURLOPT_POST, 1); 
                curl_setopt($ch,    CURLOPT_POSTFIELDS, $postdata); 
                } 

            $tmp = curl_exec ($ch); 
            curl_close ($ch); 
            } 
        return $tmp; 
        } 

    function tagSearch($line){
        $tag = explode("<", $line)[1];
        $tag = explode(">", $tag)[0];
        $tag = explode(" ", $tag)[0];
        return $tag;
    }

    function isTagInLine($line, $tag){
        return strpos($line, $tag) !== false;
    }


    function tagParseValues($line, $tag, $value){
        $content = explode($tag, $line)[1];
        $content = explode(">", $content)[0];
        $content = explode($value, $content)[1];
        $content = explode("=", $content)[1];
        $content = explode("\"", $content)[1];
        return $content;
    }


    function contentForTag($line, $tag){
        $content = explode($tag, $line)[1];
        $result = "";
        $start = false;

        foreach (str_split($content) as $char) {
            if($start){
                $result = $char.$result;
            }else{
                if ($char == ">") {
                    $start = true;
                }
            }

        }
        $content = $result;
        $result = "";
        $start = false;
        foreach (str_split($content) as $char) {
            if($start){
                $result = $char.$result;
            }else{
                if ($char == "<") {
                    $start = true;
                }
            }

        }

        return $result;
    }

    function multiLineTagContent($content, $tag, $class, $value){
        $tagFound = false;
        $newLine = "";

        foreach (explode("\n", $content) as $line) {

            if($tagFound && !empty($line)){
                
                $newLine = $newLine.$line;
            }

            if(tagParseValues($line, $tag, $class)==$value){
                
                $tagFound = true;
                $newLine = $line;
                if(isTagInLine($line, "/".$tag)){
                    return contentForTag($newLine, $tag);
                }
            }

            if(isTagInLine($line, "/".$tag) && $tagFound){
                return contentForTag($newLine, $tag);

            }

        }
    }

    function deleteSpan($string){
        $split = explode("<span", $string);
        $split[1] = explode(">", $split[1])[1];
        if(!empty($split[2])){
            $split[2] = explode(">", $split[2])[1]; 

            return $split[0].$split[1].$split[2];
        }
        return $split[0].$split[1];
    }
    function deleteSup($string){
        $split = explode("<sup", $string);
        $split[1] = explode(">", $split[1])[1];
        return $split[0].",".$split[1];
    }

    function devideToppings($toppings){
        $replacements = array("mit", "und", "<sup>", "</sup>", " in ", "verschiedenem", "verschiedener", "verschiedene", "gewürzter", "<br />", "oder", "-", "überbacken", "10");

        $toppings = str_replace($replacements, "," , $toppings);
        $toppings = deleteSpan($toppings);
        $toppings = deleteSup($toppings);
        $toppings = explode(",", $toppings);
        $top = "";

        foreach ($toppings as $topping) {
            $topping = trim($topping);
            if(strlen($topping)>1){
                $top = ($top=="") ? $topping : $top.", ".$topping;
            }
        }

        return $top;
    }


    function mealParser($content, $category){

        $costs = "";
        $name = "";
        $topping = "";

        $topping = multiLineTagContent($content, "td", "class", "ArtTx");
        $topping = devideToppings($topping);

        foreach (explode("\n", $content) as $line) {
            if (tagParseValues($line, "input", "name")=="Beschreibung") {
                //name des Essens
                $name = tagParseValues($line, "input", "value");

            }elseif (tagParseValues($line, "input", "name")=="Preis"){
                //Preis
                $costs = tagParseValues($line, "input", "value");

            }



        }


        $meal = array('name' => $name,
                    'inhalt' => $topping,
                    'preis' => $costs,
                    'kategorie' => $category
        );

       return $meal;
    }


    function parseHomePageContent($content, $category){
        $menuFound = false;
        $mealFound = false;
        $meal = "";
        $mealsParsed = []; 

        foreach (explode("\n", $content) AS $line)  {
            $line = trim($line);

            if(tagSearch($line) == "/div"){
                if($menuFound){
                  $newMeal = mealParser($meal, $category);
                    array_push($mealsParsed, $newMeal);
                    $meal = "";  
                }
                
                $menuFound = false;
            }

            if($menuFound && !empty($line)){
                
                $meal = $meal.$line."\n";
            }

            if(tagParseValues($line, "div", "class")=="WarenBox"){
                $menuFound = true;
            }

        }
        return $mealsParsed;
    }

    function parseHomePageGroup($content){

        foreach (explode("\n", $content) AS $line)  {
            $line = trim($line);
            
            if(tagSearch($line) == "h1"){
                return contentForTag($line, "h1");

            }

        }
    }

    function parseWebsite($url){
        $_buffer = HomepageLaden($url, ""); 
        $category = parseHomePageGroup($_buffer);

        return parseHomePageContent($_buffer, $category);
    }

    function getWebsites(){
        return array( "http://www.pamir-pizza.de/Auflauf.php",
                    "http://www.pamir-pizza.de/Schnitzel.php",
                    "http://www.pamir-pizza.de/Indisch.php",
                    "http://www.pamir-pizza.de/Doener.php",
                    "http://www.pamir-pizza.de/Chicken.php",
                    "http://www.pamir-pizza.de/Getraenke.php"

        );
    }

    function getPamirMenu(){    
        $urls = getWebsites();
        $meals = array();
        foreach ($urls as $url) {
             $meals = array_merge($meals, parseWebsite($url));
        } 

        return $meals;
    }

    
?>
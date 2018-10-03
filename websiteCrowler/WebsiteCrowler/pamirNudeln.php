<?php 
namespace pamirN_V;

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

            # mit den nächsten 2 Zeilen könnte man auch Cookies 
            # verwenden und in einem DIR speichern 
            #curl_setopt($ch,    CURLOPT_COOKIEJAR, "cookie.txt"); 
            #curl_setopt($ch,    CURLOPT_COOKIEFILE, "cookie.txt"); 

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
    function devideToppings($toppings){
        $replacements = array("mit","gefüllt", "und", "<sup>", "</sup>", " in ", "gewürzter", "<br />", "oder", "-", "überbacken", "</span>", "</font>", "<strong>", "</strong>", "SEHR SCHARF!", "(", ")");

        $toppings = str_replace($replacements, "," , $toppings);
        $toppings = explode(",", $toppings);
        $top = "";

        foreach ($toppings as $topping) {
            $topping = trim($topping);
            if(strlen($topping)>2){
                $top = ($top=="") ? $topping : $top.", ".$topping;
            }
        }

        return $top;
    }


    function mealParser($content, $category){

        $costs = "";
        $id = "";
        $name = "";
        $topping = explode("\n", $content)[2];
        $topping = contentForTag($topping, "td");


        foreach (explode("\n", $content) as $line) {
            if (tagParseValues($line, "input", "name")=="Beschreibung") {
                //name des Essens

                $name = tagParseValues($line, "input", "value");

            }elseif (tagParseValues($line, "input", "name")=="Preis"){
                //Preis

                $costs = tagParseValues($line, "input", "value");
            }
        }

        $topping = devideToppings($topping);

        $meal = array('name' => $name,
                    'inhalt' => $topping,
                    'preis' => $costs,
                    'kategorie' => $category
        );

       return $meal;
    }


    function parseHomePageContent($content, $category){
        $menuFound = 0;
        $mealFound = false;
        $meal = "";
        $mealsParsed = []; 

        foreach (explode("\n", $content) AS $line)  {
            $line = trim($line);

            if($menuFound == 3){
                if($mealFound && !empty($line)){
                    $meal = $meal.$line."\n";
                }

                if(tagSearch($line) == "tr"){
                    $mealFound = true;
                }

                if(tagSearch($line) == "/tr"){

                    $mealFound = false;
                    $newMeal = mealParser($meal, $category);
                    array_push($mealsParsed, $newMeal);
                    $meal = "";
                }
                if(tagSearch($line) == "/table"){
                    $menuFound++;
                }
            }

            if(tagSearch($line) == "table"){
                $menuFound++;
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


    function getNudelnVorspeisen(){
        $url = "http://www.pamir-pizza.de/Nudeln.php"; 
        $meals = parseWebsite($url);

        $url = "http://www.pamir-pizza.de/Vorspeisen.php"; 
        $meals = array_merge($meals, parseWebsite($url));

        return $meals;
    }


?>
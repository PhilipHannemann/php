<?php 
namespace pamirPizza;

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


    function mealParser($content, $category){

        $p1 = $p2 = $p3 = $p4 = "";
        $name = "";
        $topping = "";


        foreach (explode("\n", $content) as $line) {
            if (tagParseValues($line, "input", "name")=="Beschreibung") {
                //name des Essens
                $name = tagParseValues($line, "input", "value");

            }elseif (tagParseValues($line, "input", "name")=="P1"){
                //Preis
                $p1 = tagParseValues($line, "input", "value");

            }elseif (tagParseValues($line, "input", "name")=="P2"){
                //Preis
                $p2 = tagParseValues($line, "input", "value");

            }elseif (tagParseValues($line, "input", "name")=="P3"){
                //Preis
                $p3 = tagParseValues($line, "input", "value");

            }elseif (tagParseValues($line, "input", "name")=="P4"){
                //Preis
                $p4 = tagParseValues($line, "input", "value");

            }elseif (tagParseValues($line, "input", "name")=="Preis"){
                //Preis
                $p4 = tagParseValues($line, "input", "value");

            }elseif (tagParseValues($line, "input", "name")=="zutaten[]"){
                //Preis
                $top = tagParseValues($line, "input", "value");
                $topping = ($topping == "") ? $top : $topping.",".$top;

            }



        }


        $meal = array('name' => $name,
                    'inhalt' => $topping,
                    'p1' => $p1,
                    'p2' => $p2,
                    'p3' => $p3,
                    'p4' => $p4,
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

            if(tagSearch($line) == "/form"){
                $menuFound = false;

                $newMeal = mealParser($meal, $category);
                array_push($mealsParsed, $newMeal);
                $meal = "";
            }

            if($menuFound && !empty($line)){
                
                $meal = $meal.$line."\n";
            }

            if(tagSearch($line) == "form"){
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

    function getPizza(){
        $url = "http://www.pamir-pizza.de/Pizza.php";  
        $meals = parseWebsite($url);
        return($meals);
    }
    
?>
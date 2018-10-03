<?php 
namespace menuBologna;
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
        $content = explode("<".$tag, $line)[1];
        $content = explode("</".$tag.">", $content)[0];
        $result = "";
        $start = false;

        foreach (str_split($content) as $char) {
            if($start){
                $result = $result.$char;
            }else{
                if ($char == ">") {
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
    function deleteFont($string){
        $split = explode("<font", $string);
        $split[1] = explode(">", $split[1])[1];
        return $split[0].$split[1];
    }
    function deleteSup($string){
        $split = explode("<sup", $string);
        $split[1] = explode(">", $split[1])[1];
        return $split[0].",".$split[1];
    }

    function devideToppings($toppings){
        $replacements = array("mit", "und", "<sup>", "</sup>", " in ", "verschiedenem", "verschiedener", "verschiedene", "gewürzter", "<br />", "oder", "-", "überbacken", "</span>", "</font>", "<strong>", "</strong>", "SEHR SCHARF!", "(", ")");

        $toppings = str_replace($replacements, "," , $toppings);
        $toppings = str_replace("\"", "" , $toppings);
        $toppings = deleteSpan($toppings);
        $toppings = deleteFont($toppings);
        $toppings = deleteSup($toppings);
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

        $content = str_replace("&nbsp;", "" , $content);
        $p1 = $p2 = $p3 = $p4 = "";
        $name = "";
        $topping = "";

        foreach (explode("\n", $content) as $line) {

            if(isTagInLine($line, "<p>")){

                $inside = contentForTag($line, "p");

                if(tagSearch($inside) == "strong"){
                    
                    $name = contentForTag($line, "strong");

                }elseif(isTagInLine($line, "<div>")){
                    
                    $topping = contentForTag($line, "p");

                }



            }elseif (tagParseValues($line, "input", "name")=="price1" ||tagParseValues($line, "input", "name")=="price"){
                //Preis 1

                $p1 = tagParseValues($line, "input", "value");

            }elseif (tagParseValues($line, "input", "name")=="price2"){
                //Preis 2

                $p2 = tagParseValues($line, "input", "value");

            }elseif (tagParseValues($line, "input", "name")=="price3"){
                //Preis 3

                $p3 = tagParseValues($line, "input", "value");

            }elseif (tagParseValues($line, "input", "name")=="price4"){
                //Preis 4

                $p4 = tagParseValues($line, "input", "value");

            }


        }

        
        $topping = devideToppings($topping);
        $name =  devideToppings($name);

        if($name == ""){
            return [];
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
        $pizza = [];

        foreach (explode("\n", $content) AS $line)  {
            $line = trim($line);

            if($menuFound && !empty($line)){
                
                $meal = $meal.$line."\n";
            }

            if(tagParseValues($line, "tr", "bgcolor")=="#cecece"||tagParseValues($line, "tr", "bgcolor")=="#f1f1f1"){
                if($menuFound){
                  $newMeal = mealParser($meal, $category);
                    if (!empty($newMeal)) {
                        if($newMeal["kategorie"]=="Pizza"|| $newMeal["kategorie"]=="Amerikanische Pizzen"){
                            array_push($pizza, $newMeal);
                        }else{
                            array_push($mealsParsed, $newMeal);
                        }
                        
                    }
                    
                    $meal = "";  
                }

                $menuFound = true;
            }

        }
        return [$mealsParsed, $pizza];
    }

    function parseHomePageGroup($content){
            $content = str_replace("Ã¶", "ö" , $content);
            $content = str_replace("Ã¤", "ä" , $content);
            $content = str_replace("Ã¼", "ü" , $content);
        foreach (explode("\n", $content) AS $line)  {
            $line = trim($line);
            if(tagParseValues($line, "div", "class")=="tah15pxred"){
                return contentForTag($line, "div");

            }

        }
    }

    function parseWebsite($url){
        $_buffer = HomepageLaden($url, ""); 
        $category = parseHomePageGroup($_buffer);

        return parseHomePageContent($_buffer, $category);
    }

    function getWebsites(){
        $urls = [];
        for ($i=1; $i <= 20; $i++) { 
            array_push($urls, "http://www.bologna-pizza.de/contents.php?mid="."$i");
        }


        return $urls;
    }

    function getBolognaMenu(){
        $urls = getWebsites();
        $meals = array();
        $pizza = array();
        $menu = [];
        foreach ($urls as $url) {
            $meals = parseWebsite($url);

            $menu = array_merge($menu, $meals[0]);
            $pizza = array_merge($pizza, $meals[1]);
        } 

        return [$menu, $pizza];
    }

    
?>
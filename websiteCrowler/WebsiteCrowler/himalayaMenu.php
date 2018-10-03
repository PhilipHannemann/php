<?php 
namespace himalayaMenu;
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
        $content1 = explode("\"", $content)[1];
        $content2 = explode("\"", $content)[0];

        if (empty($content2)) {
            return $content1;
        }else{
            return $content2;
        }
        
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
        $replacements = array("mit", "und", "<sup>", "</sup>", " in ", "verschiedenem", "verschiedener", "verschiedenen", "verschiedene", "gewürzter", "<br />", "oder", "überbacken", "</span>", "</font>", "<strong>", "</strong>", "SEHR SCHARF!", "(", ")", "</td>", "dazu", "auf", "gef&uuml;llt", "paniert", "</p>");

        $toppings = str_replace($replacements, "," , $toppings);
        $toppings = str_replace("\"", "" , $toppings);
        $toppings = deleteSpan($toppings);
        $toppings = deleteFont($toppings);
        $toppings = deleteSup($toppings);
        if(isTagInLine($toppings, "<td")){
            $topping = explode("<td", $toppings)[0];
        }
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


    function mealParser($content, $category, $topping){

        $p1 = "";
        $name = "";
        $size = "";

        foreach (explode("\n", $content) as $line) {

            if (tagParseValues($line, "input", "name")=="Preis"){
                //Preis 
                $p1 = tagParseValues($line, "input", "value");
            }elseif (tagParseValues($line, "input", "name")=="Beschreibung") {
                //Bezeichnung
                $name = tagParseValues($line, "input", "value");
            }elseif (tagParseValues($line, "input", "name")=="Gr") {
                //Größe
                $size = tagParseValues($line, "input", "value");
            }

        }

        
        $topping = devideToppings($topping);
        //$name =  devideToppings($name);

        if($name == ""){
            return [];
        }

        $meal = array('name' => $name,
                    'inhalt' => $topping,
                    'p1' => $p1,
                    'größe' => $size,
                    'kategorie' => $category
        );

       return $meal;
    }


    function parseHomePageContent($content, $category){
        $menuFound = false;
        $meal = "";
        $mealsParsed = []; 
        $lines = explode("\n", $content);
        $topping = "";
        $pizza = [];


        for ($i=0; $i < count($lines); $i++){
            $lines[$i] = trim($lines[$i]);

            if (isTagInLine($lines[$i], "</form>")) {
                if($menuFound){
                  $newMeal = mealParser($meal, $category, $topping);
                    if (!empty($newMeal)) {
                        if($newMeal["kategorie"]=="Pizza"){
                            array_push($pizza, $newMeal);
                        }else{
                            array_push($mealsParsed, $newMeal);
                        } 
                    }
                    
                    $meal = "";  
                }
            }


            
            if($menuFound && !empty($lines[$i])){
                
                $meal = $meal.$lines[$i]."\n";
            }

            if(tagParseValues($lines[$i], "<span", "class")=="Bold"){
                if(isTagInLine($lines[$i], "<br />")){
                    $oneLine = explode("<br />", trim($lines[$i]))[1];
                    if(empty($oneLine)){
                        $topping = trim($lines[$i+1]);
                        if(!isTagInLine($lines[$i+1], "</td>")){
                            $topping .= " ".trim($lines[$i+2]);
                            if(!isTagInLine($lines[$i+2], "</td>")){
                                $topping .= " ".trim($lines[$i+3]);
                            } 
                        }
                    }else{
                        $topping = explode("<", $oneLine)[0];
                         
                    }
                    
                }else{
                   if(isTagInLine($lines[$i+1], "<br />")){
                        $topping = trim($lines[$i+2]);

                        if(!isTagInLine($lines[$i+2], "</td>")){
                            $topping .= " ".trim($lines[$i+3]);
                            if(!isTagInLine($lines[$i+3], "</td>")){
                                $topping .= " ".trim($lines[$i+4]);
                            } 
                        }
                        
                    } 
                }

                
            }
            if(isTagInLine($lines[$i], "<form")){

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
            
            if(tagSearch($line) == "h1"){
                return contentForTag($line, "h1");

            }

        }
    }

    function parseWebsite($url){
        $_buffer = HomepageLaden($url, ""); 
        //$_buffer = implode('', file($url));
        //echo $_buffer;
        $category = parseHomePageGroup($_buffer);

        return parseHomePageContent($_buffer, $category);
    }

    function getWebsites(){

        return array("http://www.himalaya-pizza.de/Vorspeisen.php",
                    "http://www.himalaya-pizza.de/auflauf.php",
                    "http://www.himalaya-pizza.de/Fleisch.php",
                    "http://www.himalaya-pizza.de/Nudeln.php",
                    "http://www.himalaya-pizza.de/Pizza.php",
                    "http://www.himalaya-pizza.de/Indisch.php",
                    "http://www.himalaya-pizza.de/Baguette.php",
                    "http://www.himalaya-pizza.de/Doener.php",
                    "http://www.himalaya-pizza.de/Beilagen.php",
                    "http://www.himalaya-pizza.de/Getraenke.php"
                    );
    }

    function getHimalayaMenu(){
        $urls = getWebsites();
        $meals = array();
        $pizza = array();
        $menu = [];
        foreach ($urls as $url) {
            $meals = parseWebsite($url);

            $menu = array_merge($menu, $meals[0]);
            $pizza = array_merge($pizza, $meals[1]);
         } 

        return [$menu, $pizza] ;
    }
     
    
?>
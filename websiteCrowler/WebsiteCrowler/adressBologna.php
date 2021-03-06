<?php 
namespace adressBologna;
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
    function flash_encode($string)
   {
      $string = rawurlencode(utf8_encode($string));

      $string = str_replace("%C2%96", "-", $string);
      $string = str_replace("%C2%91", "%27", $string);
      $string = str_replace("%C2%92", "%27", $string);
      $string = str_replace("%C2%82", "%27", $string);
      $string = str_replace("%C2%93", "%22", $string);
      $string = str_replace("%C2%94", "%22", $string);
      $string = str_replace("%C2%84", "%22", $string);
      $string = str_replace("%C2%8B", "%C2%AB", $string);
      $string = str_replace("%C2%9B", "%C2%BB", $string);

      return $string;
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

 
    function parseRestaurantDetails($content){
        $content = str_replace("Ã¶", "ö" , $content);
        $content = str_replace("Ã¤", "ä" , $content);
        $content = str_replace("Ã¼", "ü" , $content);

        $name = "";
        
        $place = "";
        $website = "";

        $segments = explode("<strong>", $content);

        //Name
        $name = $segments[1];
        $name = explode("</strong>", $name)[0];

        //Ort
        $lines = explode("\n", $segments[1]);        
        $place = $lines[0] ."\n". $lines[1] ."\n". $lines[2];
        $place = str_replace(["<br>", "</strong>"], "", $place);

        $formatted = "";
        foreach (explode("\n", $place) as $ii) {
            $ii = trim($ii);
            if(!empty($ii)){
                $formatted .= ($formatted == "")? $ii : ", ".$ii;
            }
        } 
         
        $place = $formatted;

        

        return [$name, $place, "http://www.bologna-pizza.de/"];
    }

    function parsePamirWebsite($url){
        $_buffer = HomepageLaden($url, ""); 
        $details = parseRestaurantDetails($_buffer);
        //echo $_buffer;
        return $details;
    }

    function getWebsites(){

        return array("http://www.bologna-pizza.de/impressum.php");
    }

    function getAdressBologna(){
        $urls = getWebsites();
        $meals = array();
        $details = parsePamirWebsite($urls[0]);

        return($details);
    }

    
?>
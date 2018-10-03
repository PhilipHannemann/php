<?php

function getAllFromRestaurant($number, $array){
  $newArray = [];
  foreach ($array as $value) {
    if ($value["restaurant"]==$number) {
      array_push($newArray, $value);
    }
  }
  return $newArray;
}

function getTableCell($elem){
  if(!empty($elem["kategorie"])){
    return getTableCellMeal($elem);
  }else{
    return getTableCellPizza($elem);
  }
}

function getTableCellPizza($elem){
  $html= "<div class=\"w3-col w3-margin-bottom\">
          <h3>".$elem["pizza_name"]."</h3>
          <p class=\"w3-opacity\">".$elem["zutaten"]."</p>
          <table align=\"center\">
            <tr>
              <th>26 cm</th>
              <th>30 cm</th>
              <th>36 cm</th>
              <th>Familie</th>
            </tr>
            <tr>
              <td>".$elem["groesse_26"]."€</td>
              <td>".$elem["groesse_30"]."€</td>
              <td>".$elem["groesse_36"]."€</td>
              <td>".$elem["groesse_familie"]."€</td>
            </tr>
          </table>
        </div>";

  return $html;
}

function getTableCellMeal($elem){

  $html= "<div class=\"w3-col w3-margin-bottom\">
          <h3>".$elem["speise_name"]."</h3>
          <p class=\"w3-opacity\">".$elem["zutaten"]."</p>
          <p><b>Preis: </b>".$elem["preis"]."€</p>
        </div>";

  return $html;
}

function checkForEqualaty($elem1, $elem2){
  if(!empty($elem1["kategorie"])){
    return checkForEqualatyMeals($elem1, $elem2);
  }else{
    return checkForEqualatyPizza($elem1, $elem2);
  }
}

function checkForEqualatyMeals($elem1, $elem2){
  similar_text($elem1["speise_name"], $elem2["speise_name"], $perc);
  if($perc > 80){
    return true;
  }

  return false;
}

function checkForEqualatyPizza($elem1, $elem2){
  similar_text($elem1["pizza_name"], $elem2["pizza_name"], $perc);

  if($perc > 80){

    return true;
  }

  return false;
}

function getRow($rest1, $rest2, $rest3){
  $html = "
  <tr>
    <td>".$rest1."</td>
    <td>".$rest2."</td>
    <td>".$rest3."</td>
  </tr>";
  return $html;
}

function tableForCategory($category, $array){
  $html = "";


  if (!empty($array)) {
    echo "<h1 class=\"w3-xxlarge w3-text-white\"><span class=\"w3-padding w3-black w3-opacity-min\"><b>$category</b></span></h1>";
    $html = "
<table align=\"center\" style=\"width: 80%\">
  <colgroup>
      <col width=\"30%\">
      <col width=\"30%\">
      <col width=\"30%\">
    </colgroup>
    <tr>
      <th>Himalaya</th>
      <th>Pamir</th>
      <th>Bologna</th>
    </tr>";


    $max = count($array);

    for ($i=0; $i < $max; $i++) { 
      $meal = [1 => "", 2 => "", 3 => ""];
        if($i < $max-1 && checkForEqualaty($array[$i], $array[$i+1])){

          if ($i < $max-1 && checkForEqualaty($array[$i], $array[$i+2])) {
              $meal[$array[$i]["restaurant"]] = getTableCell($array[$i]);
              $meal[$array[$i+1]["restaurant"]] = getTableCell($array[$i+1]);
              $meal[$array[$i+2]["restaurant"]] = getTableCell($array[$i+2]);

              $html .= getRow($meal[1], $meal[2], $meal[3]);


            $i+=2;
          }else{
            $meal[$array[$i]["restaurant"]] = getTableCell($array[$i]);
            $meal[$array[$i+1]["restaurant"]] = getTableCell($array[$i+1]);

            $html .= getRow($meal[1], $meal[2], $meal[3]);

            $i++;
          }
        }else{
          $meal[$array[$i]["restaurant"]] = getTableCell($array[$i]);

          $html .= getRow($meal[1], $meal[2], $meal[3]);
        }
        
    }

    $html .= "</table>";

    echo $html;

  }
}



?>
<!DOCTYPE html>
<html>
<title>My Web</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="css/w3.css">
<style>

  td, th{
      text-align: center;
      padding: 8px;
  }

  tr:nth-child(even) {
      background-color: #dddddd;
  }

</style>

<body>

<!-- Navbar (sit on top) -->
<div class="w3-top">
  <div class="w3-bar w3-white w3-wide w3-padding w3-card">
    <a href="index.php" class="w3-bar-item w3-button"><b>DB</b> Projekt</a>
    <!-- Float links to the right. Hide them on small screens -->
    <div class="w3-right w3-hide-small">
      <a href="#" class="w3-bar-item w3-button">Speisekarte</a>
      <a href="restaurants.php" class="w3-bar-item w3-button">Restaurants</a>
    </div>
  </div>
</div>

<br>

<!-- Page content -->
<div class="w3-content w3-padding" style="max-width:1564px">
  <center>
  <div class="w3-container w3-padding-32" id="projects" style="width: 60%">
    <h1 class="w3-border-bottom w3-border-light-grey w3-padding-16">Speisekarte</h1>
    <form action="speisekarte.php" METHOD=POST>
      <input class="w3-input w3-border w3-padding w3-card" type="text" placeholder="Suche ..." name="searchInput" id="myInput">
    </form>
    </center>
  </div>
  
<?PHP 
include("php/table.php");
 if (isset($_POST ["searchInput"])) 
 { 
   $input = $_POST ["searchInput"]; 
 } 
 else 
 { 
   $input = ""; 
 } 


// Restaurants aus der REST API extrahieren
  $json = [];
  if ($input!="") {
   $json = file_get_contents('http://philip.cplusplustutorials.de/REST/pizza/read.php?s='.$input);
  }else{
    $json = file_get_contents('http://philip.cplusplustutorials.de/REST/pizza/read.php');
  }
  

  $data = json_decode($json,true);

  tableForCategory("Pizza", $data["Pizzen"]);

  if ($input!="") {
   $json = file_get_contents('http://philip.cplusplustutorials.de/REST/speisen/read.php?s='.$input);
  }else{
    $json = file_get_contents('http://philip.cplusplustutorials.de/REST/speisen/read.php');
  }

  

  $data = json_decode($json,true);
  $categoryArr = [];
  $catagory = $data["Speisen"][0]["kategorie"];
  foreach ($data["Speisen"] as $value) {
    if ($value["kategorie"]!=$catagory) {
      tableForCategory($catagory, $categoryArr);
      $catagory = $value["kategorie"];
      $categoryArr = [];
    }
    array_push($categoryArr, $value);
  }



?> 



  
<!-- End page content -->
</div>



<!-- Footer -->
<footer class="w3-center w3-black w3-padding-16">
  <p><b>DB</b> Projekt von Philip Hannemann</a></p>
</footer>



<script>
  /*
  function callPHPScript() {
      var iofield = $('input[name=searchInput]').val();

      $.ajax({
            type: 'POST',
            async: false,   
            url: 'search.php',
            data: ({
                a: 'startSearch',
                b: iofield
            }),
            beforeSend: function(){
              document.getElementById("content").innerHTML = "<center>Datenbank lädt, bitte warten ... </center>";
            },
            success: function (data) {
            // die textausgabe zurück ins feld schreiben
              document.getElementById("content").innerHTML = data;
            }
        });

  }

  callPHPScript();*/

</script>



</body>
</html>
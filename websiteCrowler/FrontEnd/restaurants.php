<!DOCTYPE html>
<html>
<title>My Web</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="css/w3.css">

<body>

<!-- Navbar (sit on top) -->
<div class="w3-top">
  <div class="w3-bar w3-white w3-wide w3-padding w3-card">
    <a href="index.php" class="w3-bar-item w3-button"><b>DB</b> Projekt</a>
    <!-- Float links to the right. Hide them on small screens -->
    <div class="w3-right w3-hide-small">
      <a href="speisekarte.php" class="w3-bar-item w3-button">Speisekarte</a>
      <a href="#" class="w3-bar-item w3-button">Restaurants</a>
    </div>
  </div>
</div>

<br>

<!-- Page content -->
<div class="w3-content w3-padding" style="max-width:1564px">

  <!-- Project Section -->
  <div class="w3-container w3-padding-32" id="projects">
    <h3 class="w3-border-bottom w3-border-light-grey w3-padding-16">von folgenden Restaurants wurden die Speisekarten verwentet:</h3>
  </div>

  <br>

  
  <?php  

    // Restaurants aus der REST API extrahieren

    $json = file_get_contents('http://philip.cplusplustutorials.de/REST/restaurant/read.php');

    $data = json_decode($json,true);

  ?>

  <div class="w3-row-padding w3-grayscale">
     <?php  
        if (!empty($data["Restaurant"])) {
          foreach ($data["Restaurant"] as $value) {
            echo "<div class=\"w3-col l3 m6 w3-margin-bottom\">";
            echo "<h3>".$value["rest_name"];

            if (strlen($value["rest_name"]) < 28){
              echo "<br>&nbsp;";
            }

            echo "</h3>";

            echo "<p class=\"w3-opacity\">";

            $adressLines = explode(",", $value["adresse"]);

            foreach ($adressLines as $line) {
              echo trim($line). "<br>";
            }

            echo "</p>";

            echo "<p><button onclick=\"window.location.href='".$value["webseite"]."'\" class=\"w3-button w3-light-grey w3-block\">Webseite öffnen</button></p>";

            echo "</div>";
          }
        }

      ?>
    </div>

  
<!-- End page content -->
</div>

</body>

<!-- Footer -->
<footer class="w3-center w3-black w3-padding-16" style="position:absolute;
    bottom: 0; width: 100%">
  <p><b>DB</b> Projekt von Philip Hannemann</a></p>
</footer>



</body>
</html>
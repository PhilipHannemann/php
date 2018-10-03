<?php 

    $DBhost = "localhost";//"moosserver.mooswitz.rocks";
    $DBuser = "philip";
    $DBpassword = "datenbank";
    $conn = new mysqli($DBhost, $DBuser, $DBpassword, "philipsprojekt");

    function searchForKey($array, $name){
        foreach ($array as $key => $value) {
            if ($value["name"] == $name) {
                return $key;
            }
        }
        return false;
    }

    /*
        return:
            true wenn beide Arrays gleich sind
            false wenn sie sich unterscheiden
    */
    function compare($arrayIN, $arrayDB){
        $key = array_diff($arrayIN, $arrayDB);
        return empty($key);
    }

    function changed($arrayIN, $arrayDB){
        $deleted = [];
        $addad = [];
        $changed = [];     

        foreach ($arrayDB as $value) {
            $found = array_search($value[0], array_column($arrayIN, 'name'));
            if($found  === false){
                array_push($deleted, $value);
            }else{
                $key = searchForKey($arrayIN, $value[0]);
                if($key !== false){
                    if (!compare($arrayIN[$key], $value)){
                        array_push($changed, $arrayIN[$key]);
                    }
                }
            }

        }

        foreach ($arrayIN as $value) {
            $found = "";
            $found = array_search($value["name"], array_column($arrayDB, 0));
            if($found  === false){
                array_push($addad, $value);
            }
        }


        return [$addad, $deleted, $changed];
    }


    function getSqlResult($result){

        $getTable = array();

        while ($zeile = $result->fetch_assoc())
        {
            array_push($getTable, $zeile);
        }
        return $getTable;
    }

    function insertIntoDBPizza($menu, $conn, $id){

        foreach ($menu as $pizza) {
            $sql = "INSERT INTO Pizza_tbl(pizza_name, zutaten, groesse_26, groesse_30, groesse_36, groesse_familie, eintragDatum, restaurant) VALUES (\"".$pizza["name"]."\", \"".$pizza["inhalt"]."\", \"".$pizza["p1"]."\", \"".$pizza["p2"]."\", \"".$pizza["p3"]."\", \"".$pizza["p4"]."\", CAST('". date('Y-m-d')."' AS DATE), ".$id.");";

            if(!$result = $conn->query($sql)){
                echo "SQL Error: Tabel Pizza - INSERT";
            }

        }
    }

    function insertIntoDBMenu($menu, $conn, $id){

        foreach ($menu as $meal) {
            $sql = "INSERT INTO Speisen_tbl(speise_name, zutaten, preis, kategorie, eintragDatum, restaurant) VALUES (\"".$meal["name"]."\", \"".$meal["inhalt"]."\", \"".$meal["preis"]."\", \"".$meal["kategorie"]."\", CAST('". date('Y-m-d')."' AS DATE), ".$id.");";

            if(!$result = $conn->query($sql)){
                echo "SQL Error: Tabel Speisen - INSERT";
            }

        }
    }

    function removeFromDBPizza($menu, $conn, $id){
        foreach ($menu as $pizza) {
            $sql = "DELETE FROM Pizza_tbl WHERE pizza_name='".$pizza["name"]."' AND restaurant = ".$id.";";

            if(!$result = $conn->query($sql)){
                echo "SQL Error: Tabel Pizza - DELETE";
            }

        }
    }

    function removeFromDBMenu($menu, $conn, $id){
        foreach ($menu as $meal) {
            $sql = "DELETE FROM Speisen_tbl WHERE speise_name='".$meal["name"]."' AND restaurant = ".$id.";";

            if(!$result = $conn->query($sql)){
                echo "SQL Error: Tabel Speisen - DELETE";
            }
        }
    }

    function changeDBcolomsPizza($menu, $conn, $id){
        foreach ($menu as $pizza) {
            $sql = "UPDATE Pizza_tbl SET eintragDatum = CAST('". date('Y-m-d')."' AS DATE), zutaten = \"".$pizza["inhalt"]."\", groesse_26 = \"".$pizza["p1"]."\", groesse_30 = \"".$pizza["p2"]."\", groesse_36 = \"".$pizza["p3"]."\", groesse_familie = \"".$pizza["p4"]."\" WHERE pizza_name='".$pizza["name"]."' AND restaurant = ".$id.";";

            if(!$result = $conn->query($sql)){
                echo "SQL Error: Tabel Pizza - UPDATE";
            }
        }
    }

    function changeDBcolomsMenu($menu, $conn, $id){
        foreach ($menu as $meal) {
            $sql = "UPDATE Speisen_tbl SET eintragDatum = CAST('". date('Y-m-d')."' AS DATE), zutaten = \"".$meal["inhalt"]."\", preis = \"".$meal["preis"]."\", kategorie = '".$meal["kategorie"]."' WHERE pizza_name='".$meal["name"]."' AND restaurant = ".$id.";";

            if(!$result = $conn->query($sql)){
                echo "SQL Error: Tabel Speisen - UPDATE";
            }
        }
    }


    function changeTableForEquation($array){
        $result = [];

        $max = count($array);

        for ($i=0; $i < $max; $i++) {
            $value = $array[$i];
            $newElement = array(
            $value["pizza_name"],
            $value["zutaten"],
            number_format($value["groesse_26"], 2, '.', ''),
            number_format($value["groesse_30"], 2, '.', ''),
            number_format($value["groesse_36"], 2, '.', ''),
            number_format($value["groesse_familie"], 2, '.', ''),
            "Pizza"

            );

            array_push($result, $newElement);
        }
                
        return $result;

    }

    function changeTableForEquationMenu($array){
        $result = [];
        $max = count($array);

        for ($i=0; $i < $max; $i++) {
            $value = $array[$i];
            $newElement = array(
            $value["speise_name"],
            $value["zutaten"],
            number_format($value["preis"], 2, '.', ''),
            $value["kategorie"]
            );

            array_push($result, $newElement);
        }
                
        return $result;

    }

    function updatePizza($menu, $conn, $restID){
        $sql = "SELECT * FROM Pizza_tbl WHERE restaurant = \"".$restID."\";";
        $result = $conn->query($sql);
        $dbTable = getSqlResult($result);

        //addad($menu, $dbTable);

        //$sql = "UPDATE Pizza_tbl SET eintragDatum = CAST('". date('Y-m-d')."' AS DATE), restaurant = \"". $restID."\";";

        if(!$result){
            echo "SQL Error: Tabel Pizza - SELECT";
        }

        $dbTable = changeTableForEquation($dbTable);

        $chaned = changed($menu, $dbTable);

        //$toRemove = [];
        //$length = count($menu);

        /*for ($i=$length-1; $i >= $length-10; $i--) { 
            array_push($toRemove, $menu[$i]);
        }

        print_r($toRemove);
        echo "\n\n";*/
        
        removeFromDBPizza($chaned[1], $conn, $restID);   
        insertIntoDBPizza($chaned[0], $conn, $restID);
        changeDBcolomsPizza($chaned[2], $conn, $restID);


        /*
        foreach ($menu as $pizza) {
            $sql = "INSERT INTO Pizza_tbl(pizza_name, zutaten, groesse_26, groesse_30, groesse_36, groesse_familie, eintragDatum) VALUES (\"".$pizza["name"]."\", \"".$pizza["inhalt"]."\", \"".$pizza["p1"]."\", \"".$pizza["p2"]."\", \"".$pizza["p3"]."\", \"".$pizza["p4"]."\", CAST('". date('Y-m-d')."' AS DATE));";

            print_r($conn->query($sql));

        }*/
    }

    function updateMenu($menu, $conn, $restID){
        //print_r($dbTable);
        $sql = "SELECT * FROM Speisen_tbl WHERE restaurant = \"".$restID."\";";
        $result = $conn->query($sql);

        if(!$result){
            echo "SQL Error: Tabel Speisen - SELECT";
        }

        $dbTable = getSqlResult($result);
        $dbTable = changeTableForEquationMenu($dbTable);

        $chaned = changed($menu, $dbTable);

        print_r($chaned);
        
        removeFromDBMenu($chaned[1], $conn, $restID);   
        insertIntoDBMenu($chaned[0], $conn, $restID);
        changeDBcolomsMenu($chaned[2], $conn, $restID);

    }

    function updateAdress($in, $conn, $restID){
        $sql = "UPDATE Restaurant_tbl SET webseite = \"".$in[2]."\", rest_name = \"".$in[0]."\", adresse = \"".$in[1]."\" WHERE rest_id = \"".$restID."\";";

        if(!$result = $conn->query($sql)){
            echo "SQL Error";
        }

    }




    include("../WebsiteCrowler/getHimalaya.php");
    include("../WebsiteCrowler/getPamir.php");
    include("../WebsiteCrowler/getBologna.php");
    
    $dataHimalaya = \Himalaya\getHimalaya();
    $dataPamir = \Pamir\getPamir();
    $dataBologna = \Bologna\getBologna();


    $pizzaHimylaya = $dataHimalaya[0];
    $pizzaPamir = $dataPamir[0];
    $pizzaBologna = $dataBologna[0];

    updatePizza($pizzaHimylaya, $conn, 1);
    updatePizza($pizzaPamir, $conn, 2);
    updatePizza($pizzaBologna, $conn, 3);


    $menuHimylaya = $dataHimalaya[1];
    $menuPamir = $dataPamir[1];
    $menuBologna = $dataBologna[1];

    updateMenu($menuHimylaya, $conn, 1);
    updateMenu($menuPamir, $conn, 2);
    updateMenu($menuBologna, $conn, 3);

    $adressHimalaya = $dataHimalaya[2];
    $adressPamir = $dataPamir[2];
    $adressBologna = $dataBologna[2];


    updateAdress($adressHimalaya, $conn, 1);
    updateAdress($adressPamir, $conn, 2);
    updateAdress($adressBologna, $conn, 3);


//$sql = "ALTER TABLE Speisen_tbl ADD CONSTRAINT restaurant FOREIGN KEY (rest_id) REFERENCES Restaurant_tbl(rest_id);";

//$sql = "ALTER TABLE Speisen_tbl ADD restaurant INT;";


/*$sql = <<<EOD
CREATE TABLE Pizza_tbl
(
   pizza_id INT NOT NULL AUTO_INCREMENT,
   pizza_name TEXT NOT NULL,
   zutaten TEXT,
   groesse_26 FlOAT,
   groesse_30 FLOAT,
   groesse_36 FLOAT,
   groesse_familie FLOAT,
   eintragDatum DATE,
   PRIMARY KEY(pizza_id)
 );
EOD;




    $arrayIN = array("a" => "grün", "rot", "blau", "rot");
    $arrayDB = array("b" => "grün", "gelb", "rot");


    $resultADD = array_diff($arrayIN, $arrayDB);
    $resultDelete = array_diff($arrayDB, $arrayIN);

    echo "hinzu: ".$resultADD[0]." löschen: ".$resultDelete[0];
*/


    //print_r($result = $conn->query($sql));
    //print_r(getSqlResult($result));

    //$sql ="SELECT * FROM Pizza_tbl;";
    //$sql ="SELECT * FROM Restaurant_tbl;";
    //$sql = "INSERT INTO Restaurant_tbl(adresse, rest_name) VALUES (\"Chemnitz\", \"Himalaya\");";
    //$result = $conn->query($sql);

    //showSqlResult($result);

    $sql ="SELECT * FROM Speisen_tbl;";

    //$sql = "INSERT INTO Restaurant_tbl(adresse, rest_name) VALUES (\"Chemnitz\", \"Himalaya\");";
   //$sql ="SELECT * FROM Restaurant_tbl;";
    //$sql = "UPDATE Restaurant_tbl SET webseite = \"www\.himalaya\.de\" WHERE rest_id = \"1\";";

    print_r($result = $conn->query($sql));
    print_r(getSqlResult($result));

    //print_r ($result->fetch_assoc());
    //print_r ($result->fetch_assoc());
    //$conn->close();
    
?>
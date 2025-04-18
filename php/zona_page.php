<?php
require_once 'db_connection.php';
require_once 'query_functions.php';
session_start();

$error_msg = null;


if(isset($_GET["ZoneID"])){

    $zoneID = sanitizeInput($_GET["ZoneID"]);

    try{
        $connection = new Connection();
        $immobili_result = getZonaImmobile($connection->getConnection(), $zoneID);
        $zone_result = getZoneInfo($connection->getConnection(), $zoneID);
    }catch(Exception $e){
        $error_msg = $e->getMessage();
    }
    finally{
        if($connection){
            $connection->closeConnection(); 
        }
    } 

    $pageHTML = file_get_contents("../html/zona_page.html");

    $row = mysqli_fetch_assoc($zone_result);

    $zone = $row["name"];
    $pageHTML = str_replace("<placeholder_zone></placeholder_zone>", $zone, $pageHTML);
    $pageHTML = str_replace("<placeholder_zoneDescription></placeholder_zoneDescription>", $row["description"], $pageHTML);
    $encoded_zone = urlencode($zone);

    $immobili_list = "";
    while($row = mysqli_fetch_assoc($immobili_result)){

        $immobileID = $row["immobileID"];
        $name = $row["name"];
        $city = $row["city"];
        $street = $row["street"];
        $surface = $row["surface"];
        $numer_of_rooms = $row["number_of_rooms"];
        $price = $row["price"];
        $img_path = $row["img_path"];

        $immobile = file_get_contents("../html/immobile_card.html");
        $immobile = str_replace("@ID", $immobileID, $immobile);
        $immobile = str_replace("@path", $encoded_zone, $immobile);
        $immobile = str_replace("@name", $name, $immobile);
        $immobile = str_replace("@img_path", $img_path, $immobile);
        $immobile = str_replace("<placeholder_name></placeholder_name>", $name, $immobile);
        $immobile = str_replace("<placeholder_street></placeholder_street>", $street, $immobile);
        $immobile = str_replace("<placeholder_city></placeholder_city>", $city, $immobile);
        $immobile = str_replace("<placeholder_surface></placeholder_surface>", $surface, $immobile);
        $immobile = str_replace("<placeholder_nRooms></placeholder_nRooms>", $numer_of_rooms, $immobile);
        $immobile = str_replace("<placeholder_price></placeholder_price>", $price, $immobile);

        $immobili_list = $immobili_list.$immobile;
    }



    $pageHTML = str_replace("<placeholder_immobiliList></placeholder_immobiliList>", $immobili_list, $pageHTML);
    $header = file_get_contents("../html/header.html");

    if(isset($_SESSION["userID"])){
        $logsign = file_get_contents("../html/menu_logged.html");
        $hamb_logsign = file_get_contents("../html/hamb_menu_logged.html");
    }
    else{
        $logsign = file_get_contents("../html/menu_not_logged.html");
        $hamb_logsign = file_get_contents("../html/hamb_menu_notLogged.html");
    }
    
    $header = str_replace("<placeholder_menu_logsign></placeholder_menu_logsign>", $logsign, $header);
    $header = str_replace("<placeholder_menuHamb></placeholder_menuHamb>", $hamb_logsign, $header);

    $pageHTML = str_replace("<placeholder_header></placeholder_header>", $header, $pageHTML);

    $footer = file_get_contents("../html/footer.html");
    $pageHTML = str_replace("<placeholder_footer></placeholder_footer>", $footer, $pageHTML);

     //stampa della pagina
     echo $pageHTML;

}else{
    header("location: index.php?error=bog");
    exit();
}

?>
<?php
require_once 'db_connection.php';
require_once 'query_functions.php';
session_start(); //inizio sessione 

$error_msg = null;
$msg_type = null;

$pageHTML = file_get_contents("../html/immobili.html");
$header = file_get_contents("../html/header.html");

try{
    $connection = new Connection();
    $zones_result = getAllZones($connection->getConnection());
}catch(Exception $e){
    $error_msg = $e->getMessage();
    $msg_type = "error";
}
finally{
    if($connection){
        $connection->closeConnection();
    }
}

$zones_list = "";
while($row = mysqli_fetch_assoc($zones_result)){

    $zoneID = $row["zonaID"];
    $name = $row["name"];

    $zone_card = file_get_contents("../html/zone_card.html");
    $zone_card = str_replace("<placeholder_name></placeholder_name>", $name, $zone_card);
    $zone_card = str_replace("@zoneID", $zoneID, $zone_card);
    $zone_card = str_replace("@name", $name, $zone_card);

    $zones_list = $zones_list.$zone_card;
}

$pageHTML = str_replace("<placeholder_zonesList></placeholder_zonesList>", $zones_list, $pageHTML);

//cambio header a seconda se l'utente è loggato oppure no
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

//eventule messaggi
if($error_msg){
    if($error_msg == 'ServerError'){
        header("location: error_page.php");
    }
}

//stampa della pagina
echo $pageHTML;
?>
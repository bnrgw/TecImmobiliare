<?php
require_once 'db_connection.php';
require_once 'query_functions.php';
session_start();

$error_msg = null;
if(isset($_SESSION["userID"])){
    try{
        $connection = new Connection();
        $immobili_result = getSavedImmobili($connection->getConnection(), $_SESSION["userID"]);
    }catch(Exception $e){
        $error_msg = $e->getMessage();
    }
    finally{
        if($connection){
            $connection->closeConnection();
        }
    }

    $immobili_list = "";
    while($row = mysqli_fetch_assoc($immobili_result)){

        $immobileID = $row["immobileID"];
        $city = $row["city"];
        $street = $row["street"];
        $surface = $row["surface"];
        $numer_of_rooms = $row["number_of_rooms"];
        $price = $row["price"];
        $img_path = $row["img_path"];
        $name = $row["name"];

        $immobile = file_get_contents("../html/immobile_card.html");
        $immobile = str_replace("@ID", $immobileID, $immobile);
        $immobile = str_replace("@path", "savedImm", $immobile);
        $immobile = str_replace("@name", $name, $immobile);
        $immobile = str_replace("@img_path", $img_path, $immobile);
        $immobile = str_replace("<placeholder_street></placeholder_street>", $street, $immobile);
        $immobile = str_replace("<placeholder_city></placeholder_city>", $city, $immobile);
        $immobile = str_replace("<placeholder_surface></placeholder_surface>", $surface, $immobile);
        $immobile = str_replace("<placeholder_nRooms></placeholder_nRooms>", $numer_of_rooms, $immobile);
        $immobile = str_replace("<placeholder_price></placeholder_price>", $price, $immobile);

        $immobili_list = $immobili_list.$immobile;
    }

    $pageHTML = file_get_contents("../html/saved_immobile.html");
    $pageHTML = str_replace("<placeholder_immobiliList></placeholder_immobiliList>", $immobili_list, $pageHTML);
    
    //modifica header
    $header = file_get_contents("../html/header.html");
    $logsign = file_get_contents("../html/menu_logged.html");
    $hamb_logsign = file_get_contents("../html/hamb_menu_logged.html");
    $header = str_replace("<placeholder_menu_logsign></placeholder_menu_logsign>", $logsign, $header);
    $header = str_replace("<placeholder_menuHamb></placeholder_menuHamb>", $hamb_logsign, $header);

    //footer
    $footer = file_get_contents("../html/footer.html");

    $pageHTML = str_replace("<placeholder_header></placeholder_header>", $header, $pageHTML);
    $pageHTML = str_replace("<placeholder_footer></placeholder_footer>", $footer, $pageHTML);

    //eventule messaggi
    if($error_msg){
        if($error_msg == 'ServerError'){
            header("location: error_page.php");
        }
    }


    //stampa della pagina
    echo $pageHTML;
}else{
    header("location: index.php");
    exit();
}

?>
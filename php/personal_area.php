<?php
require_once 'input_control_functions.php';
require_once 'db_connection.php';
require_once 'query_functions.php';
session_start();

$error_msg = null;

//controllo di essere loggato
if(isset($_SESSION["userID"])){


    //modifica header
    $header = file_get_contents("../html/header.html");
    $logsign = file_get_contents("../html/menu_logged.html");
    $hamb_logsign = file_get_contents("../html/hamb_menu_logged.html");
    $header = str_replace("<placeholder_menu_logsign></placeholder_menu_logsign>", $logsign, $header);
    $header = str_replace("<placeholder_menuHamb></placeholder_menuHamb>", $hamb_logsign, $header);

    //footer
    $footer = file_get_contents("../html/footer.html");

    //modifica del contenuto con i dati dell'utente
    $content = file_get_contents("../html/personal_area.html");
    $content = str_replace("<placeholder_name></placeholder_name>", $_SESSION["name"], $content);
    $content = str_replace("<placeholder_surname></placeholder_surname>", $_SESSION["surname"], $content);
    $content = str_replace("<placeholder_email></placeholder_email>", $_SESSION["email"], $content);
    $content = str_replace("<placeholder_regDate></placeholder_regDate>", $_SESSION["registration_date"], $content);

    $error_msg == null;


    //inserisco header e footer
    $content = str_replace("<placeholder_header></placeholder_header>", $header, $content);
    $content = str_replace("<placeholder_footer></placeholder_footer>", $footer, $content);
    

    //stampa della pagina
    $pageHTML =$content;
    echo $pageHTML;
}
else{
    header("location: index.php");
    exit();
}
?>
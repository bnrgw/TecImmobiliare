<?php
session_start(); //inizio sessione 

$pageHTML = file_get_contents("../html/about_us.html");
$header = file_get_contents("../html/header.html");

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

//selezione voce di menu attiva
/*$header = str_replace("@homeActive", "", $header);
$header = str_replace("@immActive", "", $header);
$header = str_replace("@aboutActive", "active", $header);
$header = str_replace("@contactActive", "", $header);*/

$pageHTML = str_replace("<placeholder_header></placeholder_header>", $header, $pageHTML);

$footer = file_get_contents("../html/footer.html");
$pageHTML = str_replace("<placeholder_footer></placeholder_footer>", $footer, $pageHTML);


//stampa della pagina
echo $pageHTML;

?>
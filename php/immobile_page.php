<?php
require_once 'db_connection.php';
require_once 'query_functions.php';
session_start();

$error_msg = null;
$msg_type = null;

if(isset($_GET["immobileID"])){

    $pageHTML = file_get_contents("../html/immobile_page.html");
    $immobileID = sanitizeInput($_GET["immobileID"]); //sanifico l'input 
    $pageHTML= str_replace("@immobileID", $immobileID, $pageHTML);
    $path = sanitizeInput($_GET["path"]);
    $pageHTML= str_replace("@path", $path, $pageHTML);

    if(isset($_SESSION["userID"])){
        try{
            $connection = new Connection();
            $saved = isImmobileSaved($connection->getConnection(), $immobileID, $_SESSION["userID"]);
        }catch(Exception $e){
            $error_msg = $e->getMessage();
            $msg_type = "error";
        }
        finally{
            if($connection){
                $connection->closeConnection();
            }
        }
    }

    try{
        $connection = new Connection();
        $immobile_result = getImmobileInfo($connection->getConnection(), $immobileID);
    }catch(Exception $e){
        $error_msg = $e->getMessage();
        $msg_type = "error";
    }
    finally{
        if($connection){
            $connection->closeConnection(); 
        }
    } 


    $row = mysqli_fetch_assoc($immobile_result);
    $pageHTML = str_replace("<placeholder_title></placeholder_title>", $row["name"], $pageHTML);
    $pageHTML = str_replace("<placeholder_description></placeholder_description>", $row["description"], $pageHTML);
    $pageHTML = str_replace("<placeholder_city></placeholder_city>", $row["city"], $pageHTML);
    $pageHTML = str_replace("<placeholder_street></placeholder_street>", $row["street"], $pageHTML);
    $pageHTML = str_replace("<placeholder_nRooms></placeholder_nRooms>", $row["number_of_rooms"], $pageHTML);
    $pageHTML = str_replace("<placeholder_surface></placeholder_surface>", $row["surface"], $pageHTML);
    $pageHTML = str_replace("<placeholder_price></placeholder_price>", $row["price"], $pageHTML);
    $pageHTML = str_replace("@name", $row["name"], $pageHTML);
    $pageHTML = str_replace("@img_path", $row["img_path"], $pageHTML);

    if($path == "savedImm"){
        $pageHTML = str_replace("<placeholder_breadcrumb></placeholder_breadcrumb>", "<a href='../php/personal_area.php'>Area Personale</a> &raquo; <a href='../php/saved_immobile.php'>Immobili Salvati</a> &raquo; ".$row["name"], $pageHTML);
    }else{
        if($path == "offer"){
            $pageHTML = str_replace("<placeholder_breadcrumb></placeholder_breadcrumb>", "<a href='../php/index.php'>Home</a> &raquo; ".$row["name"], $pageHTML);
        }else{
            $pageHTML = str_replace("<placeholder_breadcrumb></placeholder_breadcrumb>", "<a href='../php/immobili.php'>Immobili</a> &raquo; <a href='../php/zona_page.php?ZoneID=".$row["zonaID"]."'>".$path."</a> &raquo; ".$row["name"], $pageHTML);
        }
    }
    
    if(isset($_POST["submit"])){
        if(isset($_SESSION["userID"])){
            try{
                $connection = new Connection();
                if(!$saved){
                    saveImmobile($connection->getConnection(), $immobileID, $_SESSION["userID"]);
                    $error_msg = "Immobile salvato con successo";
                    $msg_type = "success";
                    $pageHTML  = str_replace("@save/remove", "Rimuovi dai preferiti", $pageHTML);
                }else{
                    removeImmobile($connection->getConnection(), $immobileID, $_SESSION["userID"]);
                    $error_msg = "Immobile rimosso con successo";
                    $msg_type = "success";
                    $pageHTML  = str_replace("@save/remove", "Salva nei preferiti", $pageHTML);
                }
            }catch(Exception $e){
                $error_msg = $e->getMessage();
                $msg_type = "error";
            }
            finally{
                if($connection){
                    $connection->closeConnection();
                }
            }
            
        }else{
            $error_msg = "Devi eseguire l'accesso per salvare un immobile";
            $msg_type = "error";
        }
    }

    if(isset($_SESSION["userID"])){
        $logsign = file_get_contents("../html/menu_logged.html");
        $hamb_logsign = file_get_contents("../html/hamb_menu_logged.html");

        if(!$saved){
            $pageHTML  = str_replace("@save/remove", "Salva nei preferiti", $pageHTML);
        }else{
            $pageHTML  = str_replace("@save/remove", "Rimuovi dai preferiti", $pageHTML);
        }
    }else{
        $logsign = file_get_contents("../html/menu_not_logged.html");
        $hamb_logsign = file_get_contents("../html/hamb_menu_notLogged.html");

        $pageHTML  = str_replace("@save/remove", "Salva nei preferiti", $pageHTML);
    }

    $header = file_get_contents("../html/header.html");
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
        $pageHTML = str_replace("<placeholder_error></placeholder_error>", $error_msg, $pageHTML);
        $pageHTML = str_replace("@msg_type", $msg_type, $pageHTML);
    }else{
        $pageHTML = str_replace("<placeholder_error></placeholder_error>", "", $pageHTML);
        $pageHTML = str_replace("@msg_type ", "", $pageHTML);
    }

     //stampa della pagina
     echo $pageHTML;

}else{
    header("location: index.php?error=bog");
    exit();
}

?>
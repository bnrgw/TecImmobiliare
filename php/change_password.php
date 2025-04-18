<?php
require_once 'input_control_functions.php';
require_once 'db_connection.php';
require_once 'query_functions.php';
session_start();

$error_msg = null;
$msg_type = null;

//controllo di essere loggato
if(isset($_SESSION["userID"])){
    if(isset($_POST["submit"])){
        
        $old_password = $_POST["old_password"];
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];

        $old_password = sanitizeInput($old_password);
        $new_password = sanitizeInput($new_password);
        $confirm_password = sanitizeInput($confirm_password);

        try{
            //controllo degli input inseriti
            if(empty($old_password) || empty($new_password) || empty($confirm_password) ){
                throw new Exception("Errore: Riempire tutti i campi");
            }

            if(checkPassword($old_password) || checkPassword($new_password) || checkPassword($confirm_password)){
                throw new Exception("Errore: Le password devono contenere almeno 4 caratteri");
            }

            if(passwordDontMatch($new_password, $confirm_password) !== false){
                throw new Exception("Errore: Password e ripeti password non corrispondono");
            }

        }
        catch(Exception $e){
            $error_msg = $e->getMessage();
            $msg_type = "error";
        }

        if($error_msg == null){

            try{
                //cambio dati
                $connection = new Connection();
                changeUserPassword($connection->getConnection(), $old_password, $new_password, $confirm_password, $_SESSION["userID"]);
                $error_msg = "password cambiata";
                $msg_type = "success";
            }
            catch(Exception $e){
                $error_msg = $e->getMessage();
                $msg_type = "error";
            }
            finally{
                if($connection){
                    $connection->closeConnection();
                }
            }
        }

    }

    
    //modifica header
    $header = file_get_contents("../html/header.html");
    $logsign = file_get_contents("../html/menu_logged.html");
    $hamb_logsign = file_get_contents("../html/hamb_menu_logged.html");
    $header = str_replace("<placeholder_menu_logsign></placeholder_menu_logsign>", $logsign, $header);
    $header = str_replace("<placeholder_menuHamb></placeholder_menuHamb>", $hamb_logsign, $header);

    //footer
    $footer = file_get_contents("../html/footer.html");
    

    //modifica del contenuto con i dati dell'utente
    $content = file_get_contents("../html/change_password.html");
    
    //inserisco header e footer
    $content = str_replace("<placeholder_header></placeholder_header>", $header, $content);
    $content = str_replace("<placeholder_footer></placeholder_footer>", $footer, $content);
    
    //eventule messaggi
    if($error_msg){
        if($error_msg == 'ServerError'){
            header("location: error_page.php");
        }
        $content = str_replace("<placeholder_error></placeholder_error>", $error_msg, $content);
        $content = str_replace("@msg_type", $msg_type, $content);
    }else{
        $content = str_replace("<placeholder_error></placeholder_error>", "", $content);
        $content = str_replace("@msg_type ", "", $content);
    }

    //stampa della pagina
    $pageHTML = $content;
    echo $pageHTML;
}
else{
    header("location: index.php");
    exit();
}
?>
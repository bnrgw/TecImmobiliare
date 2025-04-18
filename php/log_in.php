<?php
require_once 'input_control_functions.php'; //funzioni controlli input
require_once 'db_connection.php';
require_once 'query_functions.php'; //funzioni che interagiscono con il db
session_start();

$error_msg = null;
$msg_type = null;
//controllo di non essere già loggato 
if(!isset($_SESSION["userID"])){

    //se è stato inviato il form
    if(isset($_POST["submit"])){

        $email = $_POST["email"];
        $pwd = $_POST["password"];

        try{
            //controllo degli input inseriti
            if(emptyInputLogin($email, $pwd) !== false){
                throw new Exception("Errore: Riempire tutti i campi");
            }

            if(invalidEmail($email) !== false){
                throw new Exception("Errore: Formato dell'email non valido");
            }
        }
        catch(Exception $e){
            $error_msg = $e->getMessage();
            $msg_type = "error";
        }

        if($error_msg == null){
            try{
                //accesso dell'utente
                $connection = new Connection();        
                loginUser($connection->getConnection(), $email, $pwd);
                header("location: personal_area.php");
                exit();
            }
            catch(Exception $e){
                $error_msg = $e->getMessage();
                $msg_type = "error";
            }
            finally{
                $connection->closeConnection();
            }
        }
    }

    //modifica header
    $header = file_get_contents("../html/header.html");
    $logsign = file_get_contents("../html/menu_not_logged.html");
    $header = str_replace("<placeholder_menu_logsign></placeholder_menu_logsign>", $logsign, $header);
    $hamb_logsign = file_get_contents("../html/hamb_menu_notLogged.html");
    $header = str_replace("<placeholder_menuHamb></placeholder_menuHamb>", $hamb_logsign, $header);

    //footer
    $footer = file_get_contents("../html/footer.html");

    //aggiunta header e footer
    $content = file_get_contents("../html/log_in.html");
    $content = str_replace("<placeholder_header></placeholder_header>", $header, $content);
    $content = str_replace("<placeholder_footer></placeholder_footer>", $footer, $content);

    if($error_msg){
        if($error_msg == 'ServerError'){
            header("location: error_page.php");
        }
        $content = str_replace("<placeholder_errorMsg></placeholder_errorMsg>", $error_msg, $content);
    }

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

    //stampa pagina
    $pageHTML = $content;
    echo $pageHTML;   
    
}
else{
    //reindirizzo alla home page se utente già loggato
    header("location: personal_area.php");
    exit();
}
?>
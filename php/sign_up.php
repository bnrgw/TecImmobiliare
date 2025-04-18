<?php
require_once 'input_control_functions.php'; //funzioni controlli input
require_once 'db_connection.php';
require_once 'query_functions.php'; //funzioni che interagiscono con il db
session_start();


$error_msg = null;
$msg_type = null;
//controllo di essere loggato
if(!isset($_SESSION["userID"])){

    //se è stato invito il form
    if(isset($_POST["submit"])){

        //recupero i dati del form
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $email = $_POST["email"];
        $pwd = $_POST["password"];
        $confirm_pwd = $_POST["confirm_pwd"];
        
        $name = sanitizeInput($name);
        $surname= sanitizeInput($surname);
        $email = sanitizeInput($email);
        $pwd= sanitizeInput($pwd);
        $confirm_pwd = sanitizeInput($confirm_pwd);

        try{
            //controllo degli input inseriti
            if(emptyInputSignup($name, $surname, $email, $pwd, $confirm_pwd) !== false){
                throw new Exception("Errore: Riempire tutti i campi");
            }

            if(invalidEmail($email) !== false){
                throw new Exception("Errore: Formato dell'email non valido");
            }

            if(checkNameSurname($name) || checkNameSurname($surname)){
                throw new Exception("Errore: Nome e cognome devono essere al massimo di 32 caratteri ciascuno");
            }

            if(checkPassword($pwd) || checkPassword($confirm_pwd)){
                throw new Exception("Errore: Password deve contenere almeno 4 caratteri");
            }

            if(passwordDontMatch($pwd, $confirm_pwd) !== false){
                throw new Exception("Errore: Password e conferma password non corrispondono");
            }
        }
        catch(Exception $e){
            $error_msg = $e->getMessage();
            $msg_type = "error";
        }

        if($error_msg == null){

            try{
                $connection = new Connection();
                if(emailExists($connection->getConnection(), $email) !== false){
                    throw new Exception("Email già in uso");
                }
                
                //creazione nuovo utente
                createUser($connection->getConnection(), $name, $surname, $email, $pwd);
                header("location: index.php");
                exit();
                
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
    $logsign = file_get_contents("../html/menu_not_logged.html");
    $header = str_replace("<placeholder_menu_logsign></placeholder_menu_logsign>", $logsign, $header);
    $hamb_logsign = file_get_contents("../html/hamb_menu_notLogged.html");
    $header = str_replace("<placeholder_menuHamb></placeholder_menuHamb>", $hamb_logsign, $header);

    //footer
    $footer = file_get_contents("../html/footer.html");


    //aggiunto header e footer
    $content = file_get_contents("../html/sign_up.html");
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


    //stampa pagina
    $pageHTML = $content;
    echo $pageHTML;

}else{
    header("location: index.php");
    exit();

}
?>

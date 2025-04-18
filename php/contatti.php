<?php
require_once 'input_control_functions.php';
require_once 'db_connection.php';
require_once 'query_functions.php';
session_start(); //inizio sessione 

$error_msg = null;
$msg_type = null;

$pageHTML = file_get_contents("../html/contatti.html");
$header = file_get_contents("../html/header.html");

//cambio header a seconda se l'utente è loggato oppure no
if(isset($_SESSION["userID"])){
    $logsign = file_get_contents("../html/menu_logged.html");
    $hamb_logsign = file_get_contents("../html/hamb_menu_logged.html");

    $pattern = "/<placeholder_review>.*?<\/placeholder_review>/s";
    $review_form= file_get_contents("../html/review.html");
    $deleteReview = file_get_contents("../html/delete_review.html");
    $pageHTML = preg_replace($pattern, $review_form, $pageHTML);

    $retrived_review = null;

    try{
        $connection = new Connection();
        $retrived_review_row = retriveReview($connection->getConnection(), $_SESSION["userID"]);
    }catch(Exception $e){
        $error_msg = $e->getMessage();
        $msg_type = "error";
    }
    finally{
        if($connection){
            $connection->closeConnection();
        }
    }

    

    if(isset($_POST["submit"])){
        $review= $_POST["review"];

        try{

            $connection = new Connection();
            
            if(emptyInputReview($review) !== false){
                throw new Exception("Errore: Recensione vuota");
            }

            if($retrived_review_row === false){
                addReview($connection->getConnection(), $_SESSION["userID"], $review);
                $error_msg = "Recensione aggiunta con successo";
                $msg_type = "success";
            }else{
                updateReview($connection->getConnection(), $_SESSION["userID"], $review);
                $error_msg = "Recensione modificata con successo";
                $msg_type = "success";
            }

            $pageHTML = str_replace("<placeholder_oldReview></placeholder_oldReview>", $review, $pageHTML);
            $pageHTML = str_replace("@button_value", "Modifica", $pageHTML);
            $pageHTML = str_replace("<placeholder_deleteReview></placeholder_deleteReview>", $deleteReview, $pageHTML);
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

    if(isset($_POST["delete"])){
        if($retrived_review_row !== false){
            try{
                $connection = new Connection();
                removeReview($connection->getConnection(), $_SESSION["userID"]);
                $error_msg = "Recensione eliminata con successo";
                $msg_type = "success";
                $pageHTML = str_replace("<placeholder_oldReview></placeholder_oldReview>", "Non hai lasciato nessuna recensione", $pageHTML);
                $pageHTML = str_replace("@button_value", "Invia", $pageHTML);
                $pageHTML = str_replace("<placeholder_deleteReview></placeholder_deleteReview>", "", $pageHTML);
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
            $error_msg = "Non hai nessuna recensione da eliminare";
            $msg_type = "error";
        }
    }

    if($retrived_review_row !== false){
        $pageHTML = str_replace("<placeholder_oldReview></placeholder_oldReview>", $retrived_review_row["content"], $pageHTML);
        $pageHTML = str_replace("@button_value", "Modifica", $pageHTML);
        $pageHTML = str_replace("<placeholder_deleteReview></placeholder_deleteReview>", $deleteReview, $pageHTML);
    }else{
        $pageHTML = str_replace("<placeholder_oldReview></placeholder_oldReview>", "Non hai lasciato nessuna recensione", $pageHTML);
        $pageHTML = str_replace("@button_value", "Invia", $pageHTML);
        $pageHTML = str_replace("<placeholder_deleteReview></placeholder_deleteReview>", "", $pageHTML);
    }
}
else{

    $logsign = file_get_contents("../html/menu_not_logged.html");
    $hamb_logsign = file_get_contents("../html/hamb_menu_notLogged.html");

    $pageHTML = str_replace("<placeholder_review>", "", $pageHTML);
    $pageHTML = str_replace("</placeholder_review>", "", $pageHTML);
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
    $pageHTML = str_replace("<placeholder_error></placeholder_error>", $error_msg, $pageHTML);
    $pageHTML = str_replace("@msg_type", $msg_type, $pageHTML);
}else{
    $pageHTML = str_replace("<placeholder_error></placeholder_error>", "", $pageHTML);
    $pageHTML = str_replace("@msg_type ", "", $pageHTML);
}


//stampa della pagina
echo $pageHTML;

?>
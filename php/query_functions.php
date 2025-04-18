<?php
require_once 'input_control_functions.php';

function emailExists($conn, $email){

    //pulizia degli input
    $email = sanitizeInput($email);

    //preparazione query
    $query = "SELECT * FROM person WHERE email = ?;";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $query)){
        header("location: sign_up.php?error=stmtFailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $email);

    //esecuzione query
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    //se esiste l'email nel database ritorna l'utente legato ad essa, altrimenti ritorna false
    if($row){ 
        return $row; 
    }
    else{
        return false;

    }
}

//funzioni per l'accesso

function loginUser($conn, $email, $pwd){

    //pulizia input
    $email = sanitizeInput($email);
    $pwd = sanitizeInput($pwd);

    //controllo email
    $emailExist = emailExists($conn, $email);
    if($emailExist === false){
       throw new Exception("Errore: Credenziali errate");
    }

    //controllo password e accesso
    $userPwd = $emailExist["password"];
    $checkPwd = password_verify($pwd, $userPwd);
    if($checkPwd === false){
        throw new Exception("Errore: Credenziali errate");
    }
    else{
        //creazione sessione di accesso
        session_start();
        $_SESSION["userID"] = $emailExist["userID"];
        $_SESSION["email"] = $emailExist["email"];
        $_SESSION["name"] = $emailExist["name"];
        $_SESSION["surname"] = $emailExist["surname"];
        $_SESSION["registration_date"] = $emailExist["registration_date"];
    }

}

function createUser($conn, $name, $surname, $email, $pwd){

    //pulizia degli input
    $name = sanitizeInput($name);
    $surname = sanitizeInput($surname);
    $email = sanitizeInput($email);
    $pwd = sanitizeInput($pwd);

    //preparazione query
    $query = "INSERT INTO person (email, password, name, surname, registration_date) VALUES (?, ?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $query)){
        throw new Exception("ServerError");
    }

    $current_date = new DateTime();
    $reg_date = $current_date->format('Y-m-d');

    $hashed_pwd = password_hash($pwd, PASSWORD_DEFAULT);
    mysqli_stmt_bind_param($stmt, "sssss", $email, $hashed_pwd, $name, $surname, $reg_date);

    //esecuzione query
    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("ServerError");       
    }
    mysqli_stmt_close($stmt);
}

function getSavedImmobili($conn, $userID){

    //pulizia input
    $userID = sanitizeInput($userID);

    //preparazione query
    $query = "SELECT immobile.name, immobile.immobileID, city, street, surface, number_of_rooms, price, immobile.img_path
    FROM immobile, immobili_salvati
    WHERE immobile.immobileID = immobili_salvati.immobileID AND immobili_salvati.userID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $query)){
        throw new Exception("ServerError");
    }
    mysqli_stmt_bind_param($stmt, "s", $userID);
    //esecuzione query
    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("ServerError");       
    }
    $result = mysqli_stmt_get_result($stmt);
    
    return $result;

}

function getAllZones($conn){

    //preparazione query
    $query = "SELECT zonaID, name
    FROM Zona;";

    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $query)){
        throw new Exception("ServerError");
    }

    //esecuzione query
    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("ServerError");       
    }
    $result = mysqli_stmt_get_result($stmt);

    return $result;

}

function getZoneInfo($conn, $zonaID){

    //pulizia input
    $zonaID = sanitizeInput($zonaID);

    //preparazione query
    $query = "SELECT name, description
    FROM Zona
    WHERE zonaID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $query)){
        throw new Exception("ServerError");
    }
    mysqli_stmt_bind_param($stmt, "s", $zonaID);

    //esecuzione query
    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("ServerError");       
    }
    $result = mysqli_stmt_get_result($stmt);

    return $result;

}
function getZonaImmobile($conn, $zonaID){

    //pulizia input
    $zonaID = sanitizeInput($zonaID);

    //preparazione query
    $query = "SELECT immobileID, name, city, street, surface, number_of_rooms, price, img_path
    FROM immobile
    WHERE zonaID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $query)){
        throw new Exception("ServerError");
    }
    mysqli_stmt_bind_param($stmt, "s", $zonaID);

    //esecuzione query
    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("ServerError");       
    }
    $result = mysqli_stmt_get_result($stmt);

    return $result;

}

function getImmobileInfo($conn, $immobileID){

    //pulizia input
    $immobileID = sanitizeInput($immobileID);

    //preparazione query
    $query = "SELECT zonaID, name, city, street, surface, number_of_rooms, price, description, img_path
    FROM immobile
    WHERE immobileID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $query)){
        throw new Exception("ServerError");
    }
    mysqli_stmt_bind_param($stmt, "s", $immobileID);

    //esecuzione query
    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("ServerError");       
    }
    $result = mysqli_stmt_get_result($stmt);

    return $result;

}

function saveImmobile($conn, $immobileID, $userID){

    //pulizia input
    $immobileID = sanitizeInput($immobileID);
    $userID = sanitizeInput($userID);
    
    if(isImmobileSaved($conn, $immobileID, $userID)){
        throw new Exception("Immobile già salvato");
    }

    //preparazione query
    $query = "INSERT INTO immobili_salvati (immobileID, userID) VALUES (?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $query)){
        throw new Exception("ServerError");
    }
    mysqli_stmt_bind_param($stmt, "ss", $immobileID, $userID);

    //esecuzione query
    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("ServerError");       
    }
    mysqli_stmt_close($stmt);

}

function removeImmobile($conn, $immobileID, $userID){

    //pulizia input
    $immobileID = sanitizeInput($immobileID);
    $userID = sanitizeInput($userID);

    if(!isImmobileSaved($conn, $immobileID, $userID)){
        throw new Exception("Immobile non presente fra i salvati");
    }

    //preparazione query
    $query = "DELETE FROM immobili_salvati WHERE immobileID = ? AND userID = ?;";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $query)){
        throw new Exception("ServerError");
    }
    mysqli_stmt_bind_param($stmt, "ss", $immobileID, $userID);

    //esecuzione query
    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("ServerError");       
    }
    mysqli_stmt_close($stmt);

}

function isImmobileSaved($conn, $immobileID, $userID){

    //preparazione query
    $query = "SELECT * FROM immobili_salvati WHERE immobileID = ? AND userID = ?;";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $query)){
        throw new Exception("ServerError");
    }
    mysqli_stmt_bind_param($stmt, "ss", $immobileID, $userID);

    //esecuzione query
    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("ServerError");       
    }
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if($row){
        return true;
    }
    else{
        return false;
    }

}

function changeUserInfo($conn, $new_name, $new_surname, $userID){

    //pulizia input
    $new_name = sanitizeInput($new_name);
    $new_surname = sanitizeInput($new_surname);
    $userID =sanitizeInput($userID);

    //preparazione query
    $query = "UPDATE person SET name = ?, surname = ? WHERE userID = ?;";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $query)){
  
        throw new Exception("ServerErroe");
    }
    mysqli_stmt_bind_param($stmt, "sss", $new_name, $new_surname, $userID);

    //esecuzione query
    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("ServerError");       
    }
    mysqli_stmt_close($stmt);

    //aggiornamento variabili di sessione
    $_SESSION["name"] = $new_name;
    $_SESSION["surname"] = $new_surname;

}

function changeUserPassword($conn, $old_password, $new_password, $confirm_password, $userID){

    //pulizia input
    $old_password = sanitizeInput($old_password);
    $new_password = sanitizeInput($new_password);
    $confirm_password = sanitizeInput($confirm_password);
    $userID = sanitizeInput($userID);

    //controllo password
    $password = getPassword($conn, $userID);
    if(password_verify($old_password, $password)){
        if(password_verify($new_password, $password)){
            throw new Exception("La nuova password non può essere uguale a quella vecchia");
        }
        if($new_password != $confirm_password){
            throw new Exception("La nuova password e ripeti password non corrispondono");
        }
       
        
        //preparazione query
        $query = "UPDATE person SET password = ?
        WHERE userID = ?;";

        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $query)){

            throw new Exception("ServerError");
        }

        $hashed_new_pwd = password_hash($new_password, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "ss", $hashed_new_pwd, $userID);

        //esecuzione query
        if(!mysqli_stmt_execute($stmt)){
            throw new Exception("ServerError");       
        }
        mysqli_stmt_close($stmt);

        
    }
    else{
        throw new Exception("Vecchia password errata");     
    }
}

function getPassword($conn, $userID){

    $userID = sanitizeInput($userID);

    $query = "SELECT password
    FROM person
    WHERE userID= ?;";

    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $query)){
        throw new Exception("ServerError");
    }
    mysqli_stmt_bind_param($stmt, "s", $userID);

    //esecuzione query
    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("ServerError");       
    }

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $row["password"];
}

function retriveReview($conn, $userID){
    
        //pulizia input
        $userID = sanitizeInput($userID);
    
        //preparazione query
        $query = "SELECT content, review_date
        FROM review
        WHERE userID = ?;";
    
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $query)){
            throw new Exception("ServerError");
        }
        mysqli_stmt_bind_param($stmt, "s", $userID);
    
        //esecuzione query
        if(!mysqli_stmt_execute($stmt)){
            throw new Exception("ServerError");       
        }
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        //se esiste la recensione la ritorna, altrimenti ritorna false
        if($row){ 
            return $row; 
        }
        else{
            return false;

        }
    
    
}

function addReview($conn, $userID, $content){
    
        //pulizia input
        $content = sanitizeInput($content);
        $userID = sanitizeInput($userID);
    
        //preparazione query
        $query = "INSERT INTO review (userID, content, review_date) VALUES (?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $query)){
            throw new Exception("ServerError");
        }
    
        $current_date = new DateTime();
        $review_date = $current_date->format('Y-m-d');
    
        mysqli_stmt_bind_param($stmt, "sss", $userID, $content, $review_date);
    
        //esecuzione query
        if(!mysqli_stmt_execute($stmt)){
            throw new Exception("ServerError");       
        }
        mysqli_stmt_close($stmt);
}

function updateReview($conn, $userID, $content){
    
        //pulizia input
        $content = sanitizeInput($content);
        $userID = sanitizeInput($userID);
    
        //preparazione query
        $query = "UPDATE review SET content = ?, review_date = ? WHERE userID = ?;";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $query)){
            throw new Exception("ServerError");
        }
    
        $current_date = new DateTime();
        $review_date = $current_date->format('Y-m-d');
    
        mysqli_stmt_bind_param($stmt, "sss", $content, $review_date, $userID);
    
        //esecuzione query
        if(!mysqli_stmt_execute($stmt)){
            throw new Exception("ServerError");       
        }
        mysqli_stmt_close($stmt);
}

function removeReview($conn, $userID){
    
        //pulizia input
        $userID = sanitizeInput($userID);
    
        //preparazione query
        $query = "DELETE FROM review WHERE userID = ?;";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $query)){
            throw new Exception("ServerError");
        }
        mysqli_stmt_bind_param($stmt, "s", $userID);
    
        //esecuzione query
        if(!mysqli_stmt_execute($stmt)){
            throw new Exception("ServerError");       
        }
        mysqli_stmt_close($stmt);
}
?>
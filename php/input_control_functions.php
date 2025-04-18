<?php

//funzione per pulizia dell'input
function sanitizeInput($input){
    $sanitizedInput = trim($input);
    $sanitizedInput = strip_tags($input);
    $sanitizedInput = htmlentities($input, ENT_QUOTES, "UTF-8");
    return $sanitizedInput;
}

//funzioni controllo input
function emptyInputSignup($name, $surname, $email, $pwd, $confirm_password){
    if(empty($name) || empty($surname) || empty($email) || empty($pwd) || empty($confirm_password)){
        return true;
    }else{
        return false;
    }
}

function invalidEmail($email){
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return true;
    }else{
        return false;
    }
}

function passwordDontMatch($pwd, $confirm_password){
    if($pwd != $confirm_password){
        return true;
    }else{
        return false;
    }
}

function emptyInputLogin($email, $pwd){
    if(empty($email) || empty($pwd)){
        return true;
    }else{
        return false;
    }
}

function checkNameSurname($input){
    if(strlen($input) > 32){
        return true;
    }else{
        return false;
    }
}

function checkPassword($input){
    if(strlen($input) < 4){
        return true;
    }else{
        return false;
    }
}

function emptyInputReview($review){
    if(empty($review)){
        return true;
    }else{
        return false;
    }
}
?>
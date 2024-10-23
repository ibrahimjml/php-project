<?php 

function validate($data){
  $data = trim($data);
$data = stripslashes($data);
$data = htmlspecialchars($data);
return $data;
}


function sessionStore($key,$value){

    $_SESSION[$key] = $value;
}


function sessionGet($key){

    return $_SESSION[$key] ?? [];
}


function removeSession($key){
    if(isset($_SESSION[$key])){
        unset($_SESSION[$key]);
    }
}

function showformdata($field){
  if(isset($_SESSION['formdata'])){
    $formdata = $_SESSION['formdata'];
    return $formdata[$field];
  }
}

<?php 

session_start();   
require_once '../db-connection.php'; 
require_once '../functions/validation.php' ;

ini_set('display_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION['islogged']) || !$_SESSION['islogged'] ){
  die("Access Denied");
}

$valid = true;
$content = validate( $_POST['content']);
$changingimage=true;

if($_SERVER['REQUEST_METHOD']=="POST"){

if(!isset($content) || empty(trim($content))){
  $valid=false;
  $errors[]= "content is empty";
}


if(!isset($_FILES['image']) || empty($_FILES['image']['name'])){
  $changingimage=false;
}

if(empty($errors)){ 
   if($changingimage){
    $imageextension = strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION));
    $imageposter = "uploads/".$_SESSION['ID']."-".bin2hex(random_bytes(6)).".".$imageextension;
    
    
    
    
    $checkimage = getimagesize($_FILES['image']['tmp_name']);
    if(!$checkimage){
      $valid=false;
      header("location:../create-post.php?error=1");
      exit();
    }
    
    while(file_exists($imageposter)){
      $imageposter = "uploads/".$_SESSION['ID']."-".bin2hex(random_bytes(6)).".".$imageextension;
    }
    
    if($_FILES['image']['size'] > 5000000){
      $valid=false;
      header("location: ../create-post.php?error=2");
      exit();
    }
    if($imageextension != "jpeg" && $imageextension != "png" && $imageextension != "jpg"){
      $valid=false;
      header("location: ../create-post.php?error=3");
      exit();
    }else{
    
    
   }
}

 if($valid){
  $sql = "INSERT INTO posts (post_text, user_id" . ($changingimage ? ", post_image" : "") . ") VALUES (?, ?" . ($changingimage ? ", ?" : "") . ")";
  $stmt = mysqli_prepare($conn,$sql);
  if($changingimage){

    mysqli_stmt_bind_param($stmt,"sis",$content,$_SESSION['ID'],$imageposter);
  }else{
    mysqli_stmt_bind_param($stmt,"si",$content,$_SESSION['ID']);
  }
  if(mysqli_stmt_execute($stmt)){
    
    if($changingimage){

      move_uploaded_file($_FILES['image']['tmp_name'],"../".$imageposter);
    }
    header("Location:../home.php");
    exit();
    
    }else{
      echo "ooops! error";
    }
     
   }
 
 }
  


}else{
    echo "wrong request method";
  }
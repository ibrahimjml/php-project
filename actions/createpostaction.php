<?php 

session_start();   
    require_once '../db-connection.php'; 
    require_once '../validation.php' ;
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    if(!isset($_SESSION['islogged']) || !$_SESSION['islogged'] ){
      die("Access Denied");
    }

$valid = true;
    if($_SERVER['REQUEST_METHOD']=="POST"){

if(!isset($_POST['txtarea']) || empty(trim($_POST['txtarea']))){
  $valid=false;
  $errors[]= "text is empty";
}

$post = htmlspecialchars( $_POST['txtarea']);
$changingimage=true;
if(!isset($_FILES['select_post_img']) || empty($_FILES['select_post_img']['name'])){
  $changingimage=false;
}
if(empty($errors)){

if($changingimage){


$imageextension = strtolower(pathinfo($_FILES['select_post_img']['name'],PATHINFO_EXTENSION));
$imageposter = "uploads/".$_SESSION['ID']."-".bin2hex(random_bytes(6)).".".$imageextension;




$checkimage = getimagesize($_FILES['select_post_img']['tmp_name']);
if(!$checkimage){
  $valid=false;
  header("location:../create-post.php?error=1");
  exit();
}

while(file_exists($imageposter)){
  $imageposter = "uploads/".$_SESSION['ID']."-".bin2hex(random_bytes(6)).".".$imageextension;
}

if($_FILES['select_post_img']['size'] > 5000000){
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

 if($valid){
  $sql = "INSERT INTO posts (post_text,user_id,post_image) VALUES (?,?,?)";
  $stmt = mysqli_prepare($conn,$sql);
  mysqli_stmt_bind_param($stmt,"sis",$post,$_SESSION['ID'],$imageposter);
  if(mysqli_stmt_execute($stmt)){
    move_uploaded_file($_FILES['select_post_img']['tmp_name'],"../".$imageposter);
    header("Location:../home.php");
    exit();
    }else{
      echo "ooops! error";
    }
     
}
 
}  else{
  $sql = "INSERT INTO posts (post_text,user_id) VALUES (?,?)";
  $stmt = mysqli_prepare($conn,$sql);
  mysqli_stmt_bind_param($stmt,"si",$post,$_SESSION['ID']);
  if(mysqli_stmt_execute($stmt)){
    
    header("Location:../home.php");
    exit();
    }else{
      echo "ooops! error";
    }
}

}
  


}else{
    echo "wrong request ";
  }
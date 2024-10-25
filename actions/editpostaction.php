<?php 

session_start();   
require_once '../db-connection.php'; 
require_once '../functions/validation.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

  if(!isset($_SESSION['islogged']) || !$_SESSION['islogged'] ){
    die("Access Denied");
  }
$errors=[];
$valid = true;
$postID = $_POST['post_id'];
$content = validate($_POST['txtarea']);

  if($_SERVER['REQUEST_METHOD']=="POST"){

if(!isset($postID) || empty(trim($postID))){
  die('post ID required');
}
$changingimage=true;
if(!isset($_FILES['picture']) || empty($_FILES['picture']['name'])){
  $changingimage=false;
}
if(!isset($content) || empty(trim($content))){
  $valid=false;
  $errors[]= "text is empty";
}

$sql_check = "SELECT post_id FROM posts WHERE user_id = ? AND post_id = ?";

      $stmt_check = mysqli_prepare($conn, $sql_check);
      mysqli_stmt_bind_param($stmt_check, "ii", $_SESSION['ID'], $post_id);
      if(mysqli_stmt_execute($stmt_check)){
          mysqli_stmt_store_result($stmt_check);
          if(mysqli_stmt_num_rows($stmt_check) === 0){
              die("you are not the poster");
          }
      }

if(!empty($errors)){

  sessionStore("errors",$errors);
  header("location:../create-post.php");
  exit();
}


if($changingimage){

$imageextension = strtolower(pathinfo($_FILES['picture']['name'],PATHINFO_EXTENSION));
$imageposter = "uploads/".$_SESSION['ID']."-".bin2hex(random_bytes(6)).".".$imageextension;





$checkimage = getimagesize($_FILES['picture']['tmp_name']);
if(!$checkimage){
  $valid=false;
  header("location:../editpost.php?err=1");
  }
while(file_exists($imageposter)){
  $imageposter = "uploads/".$_SESSION['ID']."-".bin2hex(random_bytes(6)).".".$imageextension;
  }

if($_FILES['picture']['size'] > 500000){
  $valid=false;
  header("location:../editpost.php?err=2");
   }
if($imageextension != "jpeg" && $imageextension != "png" && $imageextension != "jpg"){
  $valid=false;
  header("location:../editpost.php?err=3");
  }
}
 if($valid){
  $sql = "UPDATE posts SET post_text = ?" . ($changingimage ? ", post_image = ?" : "") . " WHERE post_id = ?";;
  $stmt = mysqli_prepare($conn,$sql);

  if($changingimage){
    mysqli_stmt_bind_param($stmt,"ssi",$content,$imageposter,$postID);
  }else{
    mysqli_stmt_bind_param($stmt,"si",$content,$postID);
  }
  if(mysqli_stmt_execute($stmt)){
    if($changingimage){
      move_uploaded_file($_FILES['picture']['tmp_name'],"../".$imageposter);
    }
    
    header("Location:../home.php");
    exit();
    }else{
      echo "database error";
    }

  }

    }else{
      echo 'wrong request method';
    }
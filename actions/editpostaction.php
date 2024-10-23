<?php 

session_start();   
    require_once '../db-connection.php'; 
    require_once '../validation.php' ;
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    if(!isset($_SESSION['islogged']) || !$_SESSION['islogged'] ){
      die("Access Denied");
    }
$errors=[];
$valid = true;
    if($_SERVER['REQUEST_METHOD']=="POST"){

    

if(!isset($_POST['postID']) || empty(trim($_POST['postID']))){
  header("location : ../editpost.php?err3");
}
$changingimage=true;
if(!isset($_FILES['picture']) || empty($_FILES['picture']['name'])){
  $changingimage=false;
}
if(!isset($_POST['txtarea']) || empty(trim($_POST['txtarea']))){
  $valid=false;
  $errors[]= "text is empty";
}

$postID = $_POST['postID'];
sessionStore("errors",$errors);
  header("location:../create-post.php");
$post = htmlspecialchars( $_POST['txtarea'], ENT_NOQUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8', /*double_encode*/false );
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

 if($valid && empty($errors)){
  $sql = "UPDATE posts SET post_text = ? ,post_image =? WHERE post_id =?";
  $stmt = mysqli_prepare($conn,$sql);
  mysqli_stmt_bind_param($stmt,"ssi",$post,$imageposter,$postID);
  if(mysqli_stmt_execute($stmt)){
    move_uploaded_file($_FILES['picture']['tmp_name'],"../".$imageposter);
    header("Location:../home.php");
    exit();
    }else{
      echo "ooops! error";
    }



  
  }


}elseif(empty($errors)){
  $sql = "UPDATE posts SET post_text = ?  WHERE post_id =?";
  $stmt = mysqli_prepare($conn,$sql);
  mysqli_stmt_bind_param($stmt,"si",$post,$postID);
  if(mysqli_stmt_execute($stmt)){
  
    header("Location:../home.php");
    exit();
    }else{
      echo "ooops! error";
    }
}


    

    }else{
      echo 'wrong request method';
    }
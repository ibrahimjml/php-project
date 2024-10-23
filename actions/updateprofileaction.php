<?php 
session_start();   
    require_once '../db-connection.php'; 
    require_once '../validation.php' ;
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

$errors=[];
$valid=true;
$changingimage=true;
$username=$email="";
    if($_SERVER['REQUEST_METHOD'] == "POST"){

      if(!isset($_SESSION['islogged']) || !$_SESSION['islogged'] ){
        die("Access Denied");
      }

if($_POST['csrf_token'] !== $_SESSION['csrf-token']){
  die('validation failed');
  exit();
}

$user_id =$_SESSION['ID'];
if($_POST['user_id'] !== $user_id){
  die('unauthorized action');
}

if(!isset($_POST['email'])|| empty(trim($_POST['email']))){
  $errors[]="please fill email";
}elseif(!isset($_POST['username'])|| empty(trim($_POST['username']))){
  $errors[]="please fill username";
}
if(!isset($_FILES['picture']) || empty($_FILES['picture']['name'])){
  $changingimage=false;
}

$username=validate($_POST['username']);
$email=validate($_POST['email']);

if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
  $valid=false;
  $errors[]="please valid an email";
}




if(!isset($_FILES['picture']) || empty($_FILES['picture']['name'])){
  $changingimage=false;
}

    //validation username exist
    $u="SELECT user_name FROM users WHERE user_name = ? AND user_id != ?";
    if($stmt = mysqli_prepare($conn,$u)){
     mysqli_stmt_bind_param($stmt,"si",$username,$user_id);
     mysqli_stmt_execute($stmt);
       mysqli_stmt_store_result($stmt);
       if(mysqli_stmt_num_rows($stmt) == 1){
         $errors[]="This username Is taken";
       }
      }

       //validation email 
    
  

       $uu="SELECT user_email FROM users WHERE user_email = ? AND user_id != ?";
       if($stmt = mysqli_prepare($conn,$uu)){
        mysqli_stmt_bind_param($stmt,"si",$email,$user_id);
        mysqli_stmt_execute($stmt);
          mysqli_stmt_store_result($stmt);
          if(mysqli_stmt_num_rows($stmt) == 1){
            $errors[]="This email Is taken";
          }
        }
    
        if (!empty($errors)) {
          sessionStore("errors", $errors);
          header("location:../editprofile.php");
          exit();
      }



if($changingimage){

$imageextension = strtolower(pathinfo($_FILES['picture']['name'],PATHINFO_EXTENSION));
$imageposter = "images/default-pic/".$_SESSION['ID']."-".bin2hex(random_bytes(14)).".".$imageextension;

$checkimage = getimagesize($_FILES['picture']['tmp_name']);

  if(!$checkimage){
    $valid=false;
    header("location:../editprofile.php?err=1");
    exit();
  }
  while(file_exists($imageposter)){
    $imageposter = "images/default-pic/".$_SESSION['ID']."-".bin2hex(random_bytes(6)).".".$imageextension;
  }
  
  if($_FILES['picture']['size'] > 500000){
    $valid=false;
    header("location:../editprofile.php?err=2");
    exit();
  }
  if (!in_array($imageextension, ["jpeg", "png", "jpg"])) {
    $valid = false;
    header("location:../editprofile.php?err=3");
    exit();
}

  if(empty($errors)){
    if($valid){
      $sql = "UPDATE users SET user_name = ? , user_email=? ,default_pic=?  WHERE user_id =?";
      $stmt = mysqli_prepare($conn,$sql);
      mysqli_stmt_bind_param($stmt,"sssi",$username,$email,$imageposter,$_SESSION['ID']);
      if(mysqli_stmt_execute($stmt)){
        move_uploaded_file($_FILES['picture']['tmp_name'],"../".$imageposter);
        $_SESSION['pic']=$imageposter;
        header("Location:../editprofile.php");
        exit();
        }else{
          echo "ooops! error";
        }

    }
  
  }   

}else{

  if(empty($errors))
{  
  $sql = "UPDATE users SET user_name = ? , user_email=?  WHERE user_id =?";
      $stmt = mysqli_prepare($conn,$sql);
      mysqli_stmt_bind_param($stmt,"ssi",$username,$email,$_SESSION['ID']);
      if(mysqli_stmt_execute($stmt)){
        $_SESSION['pic']=$imageposter;
        header("Location:../editprofile.php");
        exit();
        }else{
          echo "ooops! error";
        }
      }

}









     



}else{
  echo"wrong request method";
}
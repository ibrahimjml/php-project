<?php 
session_start();
include_once '../db-connection.php';

if(!isset($_SESSION['islogged']) || !$_SESSION['islogged'] ){
  die("Access Denied");
}

if(!isset($_SESSION['isadmin']) || !$_SESSION['isadmin']){
  die("Access Denied");
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
if(!isset($_POST['userID']) || empty(trim($_POST['userID']))){
  die("wrong parameters");
}
  $userID=$_POST['userID'];


$sql = "UPDATE users set is_admin = 1 WHERE user_id = ?";
$stmt = mysqli_prepare($conn,$sql);
 try{
       mysqli_stmt_bind_param($stmt,"i",$userID);
       mysqli_stmt_execute($stmt);
       header("location:../adminpage.php");
       exit();
}
catch(Exception $e){
          echo "Exception : ".$e->getMessage();
}


}else{
  echo"OOPS! something wrong";
}
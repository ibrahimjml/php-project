<?php 
session_start();
require_once '../db-connection.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION['islogged']) || !$_SESSION['islogged']){
  die("Access Denied");
  }

  if($_SERVER['REQUEST_METHOD'] == "POST"){
      if(!isset($_POST['post_id']) || empty(trim($_POST['post_id']))){
          die("Wrong Parameters");
      }

      $post_id = $_POST['post_id'];


      $sql_check = "SELECT post_id FROM posts WHERE user_id = ? AND post_id = ?";

      $stmt_check = mysqli_prepare($conn, $sql_check);
      mysqli_stmt_bind_param($stmt_check, "ii", $_SESSION['ID'], $post_id);
      if(mysqli_stmt_execute($stmt_check)){
          mysqli_stmt_store_result($stmt_check);
          if(mysqli_stmt_num_rows($stmt_check) === 0){
              die("you are not the poster");
          }
      }



$sql = "DELETE FROM posts WHERE post_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $post_id);
if(mysqli_stmt_execute($stmt)){
  $_SESSION['post']=$post_id;
    header("Location: ../home.php");
    exit();
} else {
    echo "Error executing: " . mysqli_stmt_error($stmt);
}
} else {
die("Wrong request method");
}

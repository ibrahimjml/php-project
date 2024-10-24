<?php
session_start();
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $errors =[];
    $email=$password="";
    require_once '../db-connection.php';
    require_once '../functions/validation.php' ;



    if($_SERVER['REQUEST_METHOD'] == "POST"){
    
        if(isset($_POST['email']) && isset($_POST['pass'])){
                
          if(empty(trim($_POST['email'])) ||
          empty(trim($_POST['pass']))){
            $_SESSION['empty']="please fill all inputs";
          }
  
          $email = validate($_POST['email']);
          $password = validate($_POST['pass']);

          if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $_SESSION['email']="please type valid email";
          }
  
        $sql = "SELECT user_id,user_name,user_password,user_salt,is_admin FROM users WHERE user_email = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            if(mysqli_stmt_bind_param($stmt, "s", $email)){
                if(mysqli_stmt_execute($stmt)){
                    $result = mysqli_stmt_get_result($stmt);
                    if($user = mysqli_fetch_assoc($result)){
                        $correctPassword = $user['user_password'];
                        $salt = $user['user_salt'];
                        $saltedpassword = $password . $salt;
                        $hashedPassword = hash("sha256", $saltedpassword);
                        //Rainbow Table Attack
                        if($hashedPassword === $correctPassword){
                          $_SESSION['islogged'] = true;
                          $_SESSION['ID']= $user['user_id'];
                          $_SESSION['eml']=$user['user_email'];
                          if($user['is_admin'] === 1){
                            $_SESSION['isadmin'] = true;
                          }
                            header("Location: ../home.php");
                        } else{
                          $_SESSION['noeml']="wrong email or password";
                        }
                    } 
                      
                    
                } else {
                    die("Error executing data");
                }
            } else {
                die("Error binding data");
            }
        } else {
            die("Error preparing statement");
        }
    
        sessionStore("errors",$errors);
            header("location:../login.php");
      
    
      

      }
     } else {
        die("Wrong request method");
      }
      
    

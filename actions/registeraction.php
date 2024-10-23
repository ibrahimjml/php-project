 <?php
    session_start();   
    require_once '../db-connection.php'; 
    require_once '../validation.php' ;
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

$errors=[];
$valid = true;
$username=$email=$password="";
     
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        
      $username = validate($_POST['username']);
      $email = validate($_POST['email']);
      $password = validate($_POST['pass']);

      $re ="/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/";
      
      if(strlen($username) <6 || strlen($username) > 15){
        $valid=false;
        $errors[]=" minimum 6 characters";
      }

      if(empty(trim($username))||empty(trim($email))||empty(trim($password))){
        $valid=false;
        $errors[]="please fill inputs";
      
      }else{
        $valid=true;
      }
    if(!preg_match($re,$password)){
      $valid=false;
      $errors[]="your password should be strong";
    }
  

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
      $valid=false;
      $errors[]="please valid an email";
    }
      //validation username exist
      $u="SELECT user_name FROM users WHERE user_name = ?";
    if($stmt = mysqli_prepare($conn,$u)){
     mysqli_stmt_bind_param($stmt,"s",$username);
     mysqli_stmt_execute($stmt);
       mysqli_stmt_store_result($stmt);
       if(mysqli_stmt_num_rows($stmt) == 1){
         $errors[]="This username Is taken";
       }else{
         $username=trim($_POST['username']);
       }
    
    }
     
       //validation email 
    
  

    $uu="SELECT user_email FROM users WHERE user_email = ?";
    if($stmt = mysqli_prepare($conn,$uu)){
     mysqli_stmt_bind_param($stmt,"s",$email);
     mysqli_stmt_execute($stmt);
       mysqli_stmt_store_result($stmt);
       if(mysqli_stmt_num_rows($stmt) == 1){
         $errors[]="This email Is taken";
       }else{
         $email=trim($_POST['email']);
       }
     }
    
     $user_def = "default_img.jpg";

        $salt = bin2hex(random_bytes(6));
        
        $saltedPassword = $password . $salt;
        $hashedPassword = hash("sha256", $saltedPassword);
if($valid == true && empty($errors)){
 
  

  $sql = "INSERT INTO users (user_name, user_email, user_password, user_salt,default_pic) VALUES (?, ?, ?, ?,?)";
        if($stmt = mysqli_prepare($conn, $sql)){
            if(mysqli_stmt_bind_param($stmt, "sssss", $username, $email, $hashedPassword, $salt,$user_def)){
                if(mysqli_stmt_execute($stmt)){

                   $successed[]="successfuly registerd";
                   sessionStore("successed",$successed);
                   header("location:../home.php");
                  
                  $_SESSION['islogged'] = true;
                
                  
                  $_SESSION['ID'] = mysqli_insert_id($conn);
                
                
                } else {
                    echo "Error executing: " . mysqli_stmt_error($stmt);
                  
                }
            } else {
                echo "Error binding: " . mysqli_stmt_error($stmt);
            }    
        } else {
            die("Error preparing statement: " . mysqli_error($conn));
        }

}else{
  sessionStore("errors",$errors);
     header("location:../register.php");
     
}

}else{
  echo "wrong request method";
}
      


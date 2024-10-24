<?php 
require "../db-connection.php";
require "../functions/validation.php";

header("Content-Type : application/json");
$tokenexpiration = 3600;
$email=$password ="";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

  $data = json_decode(file_get_contents("php://input"));
  if(isset($data->email) && isset($data->password)){
    $email = validate($data->email);
    $password = validate($data->password);

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
      echo json_encode(["message"=>"please enter valid email"]);
      http_response_code(400);
      exit();
    }
    $sql = "SELECT user_id, user_name, user_password, user_salt FROM users WHERE user_email = ?";
    if($stmt= mysqli_prepare($conn,$sql)){
      mysqli_stmt_bind_param($stmt,"s",$email);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);

      if($user = mysqli_fetch_assoc($result)){
        $corectpassword = $user['user_password'];
        $salt = $user['user_salt'];
        $saltedpassword = $password . $salt;
        $hashespassword = hash("sha256",$saltedpassword);
        // verify password
             if($hashespassword === $corectpassword){
              $token = bin2hex(random_bytes(16)); //generate token
              $expiration = time() + $tokenexpiration;
              $expirationDate = date('Y-m-d H:i:s', $expiration);

              // check if token exists
              $sqlcheck = "SELECT token FROM user_token WHERE user_id = ?";
              if($stmtcheck = mysqli_prepare($conn,$sqlcheck)){
                mysqli_stmt_bind_param($stmtcheck,'i',$user['user_id']);
                mysqli_stmt_execute($stmtcheck);
                $resultcheck = mysqli_stmt_get_result($stmtcheck);

                if(mysqli_fetch_assoc($resultcheck)){
                  $sqlupdate = "UPDATE user_token SET token = ?, expires_at = ? WHERE user_id =?";
                  if($stmtupdate = mysqli_prepare($conn,$sqlupdate)){
                    mysqli_stmt_bind_param($stmtupdate,"ssi",$token,$expirationDate,$user['user_id']);
                    mysqli_stmt_execute($stmtupdate);
                  }
                }else{
                  $sqlToken = "INSERT into user_token (user_id,token,expires_at) VALUES(?,?,?)";
                  if($stmtToken = mysqli_prepare($conn,$sqlToken)){
                     mysqli_stmt_bind_param($stmtToken,"iss",$user['user_id'],$token,$expirationDate);
                     mysqli_stmt_execute($stmtToken);
                  }
                }
              }
            
              echo json_encode([
               "message"=>"login successful",
               "Token"=>$token,
               "expires at"=>date("y-m-d H:i:s",$expiration)
              ]);
              http_response_code(200);
              exit();

             }else{
              echo json_encode(["message"=>"invalid credantials"]);
              http_response_code(401);
              exit();
             }
        }else{
          echo json_encode(["message" => "Email not found."]);
                http_response_code(404); 
                exit();
        }
    }else{
      echo json_encode(["message" => "Database error."]);
                http_response_code(500); 
                exit();
    }
  }else{
    echo json_encode(["message" => "Please provide email and password."]);
        http_response_code(400); 
        exit();
  }

}else{
  echo json_encode(["message"=>"invalid request method"]);
  http_response_code(405);
  exit();
}
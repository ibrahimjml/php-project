<?php 
require "../db-connection.php";

header("Content-Type:application/json");

if($_SERVER['REQUEST_METHOD'] == 'POST'){

  $headers =getallheaders();

  if(isset($headers['Authorization'])){
    $token = str_replace("Bearer ", "", $headers['Authorization']);

$sql = "SELECT user_id FROM user_token WHERE token = ?";
if($stmt = mysqli_prepare($conn,$sql)){
  mysqli_stmt_bind_param($stmt,"s",$token);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if($row = mysqli_fetch_assoc($result)){

    $sqldelete = "DELETE FROM user_token WHERE  token = ?";
    if ($stmtdelete = mysqli_prepare($conn, $sqldelete)) {
      mysqli_stmt_bind_param($stmtdelete, "s", $token);
      if (mysqli_stmt_execute($stmtdelete)) {

        echo json_encode(["message" => "Logout successful"]);
        http_response_code(200);
        exit();
    } else {

        echo json_encode(["message" => "Failed to revoke token"]);
        http_response_code(500);
        exit();
    }
  }
}else{
  echo json_encode(['message'=>'invalid token']);
  http_response_code(401);
  exit();
}
    
    }else{
      echo json_encode(['message'=>'databse error']);
      http_response_code(500);
      exit();
    }

    
  }else{
    echo json_encode(['message'=>'token not provided']);
    http_response_code(401);
    exit();
  }


}else{
  echo json_encode(['message'=>'invalid request method']);
  http_response_code(405);
  exit();
}

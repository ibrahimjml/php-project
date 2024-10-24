<?php 
require "../functions/getidfromtoken.php";
require "../db-connection.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);

if($_SERVER['REQUEST_METHOD'] == 'DELETE'){

  $headers = getallheaders();
  if (!isset($headers['Authorization'])) {
      echo json_encode(["message" => "Unauthorized: No token provided"]);
      http_response_code(401);
      exit();
  }
  
  $token = str_replace("Bearer ", "", $headers['Authorization']);
  $user_id = getUserId($conn, $token);
  if ($user_id === null) {
      echo json_encode(["message" => "Invalid token."]);
      http_response_code(401);
      exit();
  }
  
  $put_data = json_decode(file_get_contents("php://input"), true);
  $post_id = $put_data['post_id'];
  if (!isset($post_id) || empty(trim($post_id))) {
      echo json_encode(['message' => 'post ID required']);
      http_response_code(401);
      exit();
  }
  
  
  
  $sql_check = "SELECT post_id FROM posts WHERE post_id = ? AND user_id = ?";
  $stmt_check = $conn->prepare($sql_check);
  $stmt_check->bind_param("ii", $post_id, $user_id);
  
  if ($stmt_check->execute()) {
      $result_check = $stmt_check->get_result();
      if ($result_check->num_rows === 0) {
          echo json_encode(['message' => 'unauthorized action']);
          http_response_code(403);
          exit();
      }
  } else {
      echo json_encode(['message' => 'database error']);
      http_response_code(500);
      exit();
  }


  $sql = "DELETE FROM posts WHERE post_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $post_id);
  if($stmt->execute()){
     echo json_encode(['message'=>"deleted successfuly"]);
     http_response_code(200);
     exit();
  } else {
    echo json_encode(['message'=>"database error"]);
    http_response_code(500);
    exit();
  }


}else{
  echo json_encode(['message'=>'invalid request method']);
  http_response_code(405);
}

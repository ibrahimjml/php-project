<?php 
require "../functions/getidfromtoken.php";
require "../functions/validation.php";
require "../db-connection.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] == "POST") {

  $headers = getallheaders();
  if (!isset($headers['Authorization'])) {
      echo json_encode(["message" => "Unauthorized: No token provided"]);
      http_response_code(401);
      exit();
  }

  
  $token = str_replace('Bearer ', '', $headers['Authorization']);

  
  $user_id = getUserId($conn, $token);
  if ($user_id === null) {
      echo json_encode(["message" => "Invalid token. Post creation failed."]);
      http_response_code(401);
      exit();
  }


  $valid = true;
  $errors = [];

  if (!isset($_POST['txtarea']) || empty(trim($_POST['txtarea']))) {
      $valid = false;
      $errors[] = "Text is empty";
  }

  $post = htmlspecialchars($_POST['txtarea']);
  

  $changingimage = true;
  if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
      $imageextension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
      $imageposter = "uploads/" . $user_id . "-" . bin2hex(random_bytes(6)) . "." . $imageextension;

      $checkimage = getimagesize($_FILES['image']['tmp_name']);
      if (!$checkimage) {
          $valid= false;
          echo json_encode(['message'=>'not an image']);
          http_response_code(401);
      }

      while (file_exists($imageposter)) {
          $imageposter = "uploads/" . $user_id . "-" . bin2hex(random_bytes(6)) . "." . $imageextension;
      }

      if ($_FILES['image']['size'] > 5000000) {
          $valid = false;
          echo json_encode(['message'=>'image exceed 5mg']);
          http_response_code(401);
      }
      if ($imageextension != "jpeg" && $imageextension != "png" && $imageextension != "jpg") {
          $valid = false;
          echo json_encode(['message'=>'unsupported image type']);
          http_response_code(401);
      }
  } else {
      $changingimage = false;
  }


  if ($valid) {
      $sql = "INSERT INTO posts (post_text, user_id" . ($changingimage ? ", post_image" : "") . ") VALUES (?, ?" . ($changingimage ? ", ?" : "") . ")";
      $stmt = mysqli_prepare($conn, $sql);
      
      if ($changingimage) {
          mysqli_stmt_bind_param($stmt, "sis", $post, $user_id, $imageposter);
      } else {
          mysqli_stmt_bind_param($stmt, "si", $post, $user_id);
      }

      if (mysqli_stmt_execute($stmt)) {
          if ($changingimage) {
              move_uploaded_file($_FILES['image']['tmp_name'], "../" . $imageposter);
          }
          echo json_encode(["message" => "Post created successfully"]);
          http_response_code(201);
          exit();
      } else {
          echo json_encode(["message" => "Error creating post"]);
          http_response_code(500);
          exit();
      }
  } 
  
} else {
  echo json_encode(["message" => "Invalid request method"]);
  http_response_code(405); 
  exit();
}
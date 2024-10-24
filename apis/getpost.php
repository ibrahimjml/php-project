<?php 
require "../db-connection.php";

header("Content-Type:application/json");

if(isset($_GET['post_id'])){
  $post_id = $_GET['post_id'];

  $sql = "SELECT post_id, post_text, post_image, date_posted FROM posts WHERE post_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i",$post_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if($result->num_rows > 0){
    $post = $result->fetch_assoc();
    echo json_encode([
      "id" => $post['post_id'],
      "description" => $post['post_text'],
      "image" => $post['post_image'],
      "date_posted" => $post['date_posted']
    ]);
    http_response_code(200);
  }else{
    echo json_encode(['message'=>'no post found']);
    http_response_code(404);
  }
}else{
  echo json_encode(['message'=>"post ID is required"]);
  http_response_code(400);
}
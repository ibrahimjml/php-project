<?php
require "../db-connection.php";

header("Content-Type: application/json");

$sql = "SELECT post_id, post_text, post_image, date_posted FROM posts";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $posts = [];

    
    while ($row = $result->fetch_assoc()) {
        $posts[] = [
            "id" => $row['post_id'],
            "description" => $row['post_text'],
            "image" => $row['post_image'],
            "date_posted" => $row['date_posted']
        ];
    }

    
    echo json_encode($posts);
    http_response_code(200);
} else {
    
    echo json_encode(['message' => 'no posts']);
    http_response_code(400);
}

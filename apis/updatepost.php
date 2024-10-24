<?php 
require "../functions/getidfromtoken.php";
require "../functions/validation.php";
require "../db-connection.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 

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

    $post_id = $_POST['post_id'];

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

    $text = validate($_POST['textarea']);  

    if (!isset($text) || empty(trim($text))) {
        echo json_encode(['message' => 'textarea required']);
        http_response_code(401);
        exit();
    }

    $changingimage = true;
    $valid = true;

  
    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $imageextension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $imageposter = "uploads/" . $user_id . "-" . bin2hex(random_bytes(6)) . "." . $imageextension;

        $checkimage = getimagesize($_FILES['image']['tmp_name']);
        if (!$checkimage) {
            $valid = false;
            echo json_encode(['message' => 'not an image']);
            http_response_code(401);
            exit();
        }

        if ($_FILES['image']['size'] > 5000000) {
            $valid = false;
            echo json_encode(['message' => 'image exceeds 5MB']);
            http_response_code(401);
            exit();
        }

        if (!in_array($imageextension, ['jpeg', 'png', 'jpg'])) {
            $valid = false;
            echo json_encode(['message' => 'unsupported image type']);
            http_response_code(401);
            exit();
        }
    } else {
        $changingimage = false;
    }

    if ($valid) {
        $sql_update = "UPDATE posts SET post_text = ?" . ($changingimage ? ", post_image = ?" : "") . " WHERE post_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($changingimage) {
            $stmt_update->bind_param("ssi", $text, $imageposter, $post_id);
        } else {
            $stmt_update->bind_param("si", $text, $post_id);
        }

        if ($stmt_update->execute()) {
            if ($changingimage) {
                move_uploaded_file($_FILES['image']['tmp_name'], "../" . $imageposter);
            }
            echo json_encode(['message' => "post updated successfully"]);
            http_response_code(200);
        } else {
            echo json_encode(['message' => 'failed to update post']);
            http_response_code(500);
        }
    }

} else {
    echo json_encode(['message' => 'invalid request method']);
    http_response_code(405);
}


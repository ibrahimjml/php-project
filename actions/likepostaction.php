<?php
session_start();
require_once '../db-connection.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if (!isset($_SESSION['islogged']) || !$_SESSION['islogged']) {
    echo json_encode(['error' => 'Access Denied']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!isset($_POST['post_id']) || empty(trim($_POST['post_id']))) {
        echo json_encode(['error' => 'Wrong Parameters']);
        exit();
    }

    $post_id = $_POST['post_id'];
    
    $user_id = $_SESSION['ID'];


    $sql_check = "SELECT post_id FROM posts WHERE user_id = ? AND post_id = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $post_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) != 0) {
        echo json_encode(['error' => "You can't like your own post"]);
        exit();
    }

    // Check if the user has already liked the post
    $sql_like = "SELECT like_id FROM likes WHERE user_id = ? AND post_id = ?";
    $stmt_like = mysqli_prepare($conn, $sql_like);
    mysqli_stmt_bind_param($stmt_like, "ii", $user_id, $post_id);
    mysqli_stmt_execute($stmt_like);
    mysqli_stmt_store_result($stmt_like);

    if (mysqli_stmt_num_rows($stmt_like) != 0) {
        // Unlike the post
        $sql_delete_like = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
        $stmt_delete_like = mysqli_prepare($conn, $sql_delete_like);
        mysqli_stmt_bind_param($stmt_delete_like, "ii", $user_id, $post_id);
        mysqli_stmt_execute($stmt_delete_like);

      
    } else {
        // Like the post
        $sql_insert_like = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
        $stmt_insert_like = mysqli_prepare($conn, $sql_insert_like);
        mysqli_stmt_bind_param($stmt_insert_like, "ii", $post_id, $user_id);
        mysqli_stmt_execute($stmt_insert_like);

        //  like count
        $sql_like_count = "SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?";
        $stmt_like_count = mysqli_prepare($conn, $sql_like_count);
        mysqli_stmt_bind_param($stmt_like_count, "i", $post_id);
        mysqli_stmt_execute($stmt_like_count);
        $result = mysqli_stmt_get_result($stmt_like_count);
        $like_count = $result->fetch_assoc()['like_count'];

        echo json_encode([
            'liked' => true,
            'like_count' => $like_count
        ]);
    }
} else {
    echo json_encode(['error' => 'Wrong request method']);
}
?>
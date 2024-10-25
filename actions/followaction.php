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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['followed_id']) || empty(trim($_POST['followed_id']))) {
        echo json_encode(['error' => 'Wrong Parameters']);
        exit();
    }

    $follower_id = $_SESSION['ID'];

    $followed_id = $_POST['followed_id'];

    
    if ($follower_id == $followed_id) {
        echo json_encode(['error' => "You can't follow yourself"]);
        exit();
    }

    // Check if the user is already following the other user
    $sql_check = "SELECT * FROM follows WHERE follower_id = ? AND followed_id = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $follower_id, $followed_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) != 0) {
        // Unfollow the user
        $sql_unfollow = "DELETE FROM follows WHERE follower_id = ? AND followed_id = ?";
        $stmt_unfollow = mysqli_prepare($conn, $sql_unfollow);
        mysqli_stmt_bind_param($stmt_unfollow, "ii", $follower_id, $followed_id);
        mysqli_stmt_execute($stmt_unfollow);

        echo json_encode([
            'followed' => false
        ]);
    } else {
        // Follow the user
        $sql_follow = "INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)";
        $stmt_follow = mysqli_prepare($conn, $sql_follow);
        mysqli_stmt_bind_param($stmt_follow, "ii", $follower_id, $followed_id);
        mysqli_stmt_execute($stmt_follow);

        echo json_encode([
            'followed' => true
        ]);
    }
} else {
    echo json_encode(['error' => 'Wrong request method']);
}
?>

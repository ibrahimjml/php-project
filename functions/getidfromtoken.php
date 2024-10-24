<?php 
require "../db-connection.php";

function getUserId($conn, $token) {
  $sql = "SELECT user_id FROM user_token WHERE token = ?";
  if ($stmt = mysqli_prepare($conn, $sql)) {
      mysqli_stmt_bind_param($stmt, "s", $token);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);

      if ($row = mysqli_fetch_assoc($result)) {
          return $row['user_id']; 
      }
  }
  return null; 
}
<?php
session_start();
require 'db-connection.php';
require 'functions/validation.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$errors=[];

if(!isset($_GET['submit'])){
  if(!isset($_GET['query']) || empty(trim($_GET['query']))){
    $errors[]="please search on user";
    sessionStore("errors",$errors);
     header("location:home.php");
}

}

  $search = "%" . $_GET['query'] . "%";
  $sql = "SELECT user_id, user_name,default_pic FROM users WHERE user_name LIKE ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "s", $search);
  if(mysqli_stmt_execute($stmt)){
      $result = mysqli_stmt_get_result($stmt);
      $users = [];
      while($user = mysqli_fetch_assoc($result)){
          $users[] = $user;
      }
  }



?>

<?php include 'header.php';?>
  <?php if(!$users) {?>
    <h1>sorry user not found   </h1>
    <?php } else{?>
    <h1>You searched for: <?php echo htmlspecialchars($_GET["query"]); ?> </h1>
    <?php
    echo "<ul style='display:flex;flex-direction:column;gap:10px;list-style:none;margin-top:30px;width:200px;margin-left:auto;margin-right:auto;'>";
    foreach($users as $user){
        echo "<li style='width:100%;'>
        <img src=".$user['default_pic']." width='30px' style='border-radius:50%;margin-right:20px;'>
        <a style='text-decoration:none;font-size:20px;color:blue;' href='profile.php?id=". $user['user_id'] ."'>" . $user['user_name'] . "</a>
        </li>";
    }
    ?> 
    <?php }?>
</body>
</html>
<?php 
session_start();
require_once 'db-connection.php';
include_once 'functions/validation.php';


if(!isset($_SESSION['islogged']) || !$_SESSION['islogged'] ){
  die("Access Denied");
}


if(!isset($_GET['post_id']) || empty(trim($_GET['post_id']))){
  $error_message = "Post ID is missing or invalid.";
} else {
  $postid = $_GET['post_id'];


  $sql ="SELECT post_text, post_image FROM posts WHERE post_id = ? AND user_id = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "ii", $postid, $_SESSION['ID']);
  
  if(mysqli_stmt_execute($stmt)){
    $results = mysqli_stmt_get_result($stmt);
    if($post = mysqli_fetch_assoc($results)){
      $posttext = $post['post_text'];
      $postimage = $post['post_image'];
    } else {
      $error_message = "You are not the poster of this post.";
    }
  } else {
    $error_message = "Failed to retrieve post information.";
  }
}
?>

<?php include "header.php" ?>
<h1>Edit your post</h1>

<?php if(isset($error_message)): ?>
  <p class="error2"><?php echo $error_message; ?></p>
<?php else: ?>
  <?php foreach(sessionGet('errors') as $error): ?>
    <p class="error2"><?php echo $error; ?></p>
  <?php endforeach ?>
  <?php removeSession("errors"); ?>
  
  <form action="actions/editpostaction.php" method="POST" enctype="multipart/form-data">
    <textarea name="txtarea" class="txt" rows="5"><?php echo htmlspecialchars($posttext); ?></textarea>
    
    <?php if($postimage): ?>
      <img class="imgs" src="<?php echo($postimage); ?>" width="140px">
    <?php endif; ?>
    
    <input type="file" name="picture" class="file">
    <input type="hidden" name="postID" value="<?php echo htmlspecialchars($postid); ?>">
    <input type="submit" style="width:15%; margin:5px auto; background-color:dodgerblue; color:white" value="Update">
  </form>
<?php endif; ?>

<?php if(isset($_GET['err'])): ?>
  <?php
    switch($_GET['err']){
      case 1:
        echo "<h3>Not an image</h3>";
        break;
      case 2:
        echo "<h3>Image size exceeds 5mg</h3>";
        break;
      case 3:
        echo "<h3>Unsupported image type</h3>";
        break;
    }
  ?>
<?php endif; ?>

<button class="btn2"><a href="home.php">Back to home</a></button>
</body>
</html>

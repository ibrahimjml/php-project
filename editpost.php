<?php 
session_start();
require_once 'db-connection.php';
include_once 'validation.php';
if(!isset($_SESSION['islogged']) || !$_SESSION['islogged'] ){
  die("Access Denied");
}
if(!isset($_GET['post_id']) || empty(trim($_GET['post_id']))){
  header("location: index.php?err");
}
$postid = $_GET['post_id'];

$sql ="SELECT post_text,post_image FROM posts WHERE post_id =? AND user_id =?";
$stmt=mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt,"ii",$postid,$_SESSION['ID']);
if(mysqli_stmt_execute($stmt)){
  $results = mysqli_stmt_get_result($stmt);
  if($post = mysqli_fetch_assoc($results)){
     $posttext =$post['post_text'];
     $postimage=$post['post_image'];
  
    
  }else{
    die("you are not the poster");
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>editing post</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  
  <h1>Edit your post</h1>
  <?php foreach(sessionGet('errors') as $error):?>
    <p class="error2"><?php echo $error; ?></p>
      <?php endforeach ?>
      <?php removeSession("errors"); ?>
      
  <form action="actions/editpostaction.php" method="POST" enctype="multipart/form-data">
  
    <textarea name="txtarea"  class="txt" rows="5"><?php echo $posttext;?></textarea>
    
    <img class="imgs" src="<?php echo $postimage;?>" width="140px">
    
    <input type="file" name="picture" class="file">
    <input type="hidden" name="postID" value="<?php echo $postid; ?>">
      <input type="submit" style="width:15%;" value="Update">
    
  </form>




  <?php if(isset($_GET['err'])){
    switch($_GET['err']){
      case 1:
           echo "<h3>NOt AN Image</h3>";
           break;
     case 2:
           echo "<h3>Image size exceeds 500kb</h3>";
           break;
     case 3:
           echo "<h3>Unspupported Image Type</h3>";
           break;
    }
  }
  
  ?>

  <button class="btn"><a href="home.php">back to home</a></button>
</body>
</html>
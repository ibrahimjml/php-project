<?php 
session_start();
include_once 'validation.php';
if(!isset($_SESSION['islogged']) || !$_SESSION['islogged'] ){
  die("Access Denied");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>creating post</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include "header.php" ?>
  <div class="create-model">
    <h1>create your post</h1>
    <?php foreach(sessionGet('errors') as $error):?>
      <p class="error2"><?php echo $error; ?></p>
        <?php endforeach ?>
        <?php removeSession("errors"); ?>
    <form action="actions/createpostaction.php" method="POST" enctype="multipart/form-data">
      <textarea name="txtarea" class="txt" rows="5" ></textarea><br>
      <img src="" id="post_img"   width="200px">
      <input type="file" class="file" name="select_post_img" id="select_post_img">
        <input type="submit" style="width:15%;margin:5px auto;" value="post">
    
    </form>
  </div>
  <button class="btn2"><a href="home.php">back to home</a></button>
  <?php if(isset($_GET['error'])){
    switch($_GET['error']){
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
  <!-- <button class="btn"><a href="home.php">Go Home</a></button> -->
  <script>
    const input = document.getElementById('select_post_img');
    input.addEventListener("change",preview);
    function preview(){
      let fileobject = this.files[0];
      let filreader = new FileReader();
      filreader.readAsDataURL(fileobject);

      filreader.onload = ()=>{
        let image_src = filreader.result;
        const image = document.getElementById('post_img');
        image.setAttribute("src",image_src);
        image.setAttribute("style","display:block;margin:5px auto;");
      }
    }
  </script>
</body>
</html>
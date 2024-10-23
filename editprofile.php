<?php 
session_start();
require_once 'db-connection.php';
include_once 'validation.php';


  if(!isset($_SESSION['islogged']) || !$_SESSION['islogged'] ){
    die("Access Denied");
  }

if(empty($_SESSION['csrf-token'])){
  $_SESSION['csrf-token']= bin2hex(random_bytes(32));
}
$csrftoken= $_SESSION['csrf-token'];
if(!isset($_SESSION['csrf-token'])){
  die("access denied");
}

  $sql ="SELECT * FROM users WHERE user_id = ?" ;
  $stmt=mysqli_prepare($conn,$sql);
  mysqli_stmt_bind_param($stmt,"i",$_SESSION['ID']);
  if(mysqli_stmt_execute($stmt)){
    $results = mysqli_stmt_get_result($stmt);
    if($post = mysqli_fetch_assoc($results)){
       $username =$post['user_name'];
       $id=$_SESSION['ID'];
       $email=$post['user_email'];
      
       $img=$post['default_pic'];
      
      
    }else{
      die("you are not the poster");
    }
  }
//////////////////////////////////////////////////////////



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'"> -->
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./icomoon/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">

</head>
<body>

<?php include 'header.php';?>
<form action="actions/updateprofileaction.php" method="POST" enctype="multipart/form-data">
<div class="parent">
  <img class="img1" id="post_img" src="<?php echo $img; ?>" width="200px" >
  <input type="hidden" name="user_id" value="<?php echo $id?>">
  <input type="file" id="select_post_img" name="picture" class="file">
  <input type="text" name="username" value="<?php echo htmlspecialchars($username);?>">
  <input type="email" name="email" value="<?php echo htmlspecialchars($email);?>">
  <input type="hidden" name="csrf_token" value="<?php  echo $csrftoken;?>">
  <input type="submit"  value="Update">
</div>
</form>
<?php foreach(sessionGet('errors') as $error):?>
    <p class="error"><?php echo $error; ?></p>
      <?php endforeach ?>
      <?php removeSession("errors"); ?>


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
  <?php include 'footer.php';?>
</body>
</html>

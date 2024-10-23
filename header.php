<?php 
require_once "db-connection.php";

$sql ="SELECT default_pic,user_name FROM users WHERE user_id = ?";
$pictures=[];
$stmt =mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt,"i",$_SESSION['ID']);
if(mysqli_stmt_execute($stmt)){
  $result= mysqli_stmt_get_result($stmt);
  while($picture = mysqli_fetch_assoc($result)){
    $pictures[]=$picture;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'"> -->
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="icomoon/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body>


       <nav>
    <ul>
      <li class="frst"><img src="BT.jpg" alt="" width="42px"><span class="hideOnMobile">Blogterest</span></li>
      <?php if(isset($_SESSION['islogged']) ) :?>
        <li class="hideOnMobile"><a class="nvlivk" href="create-post.php">create post</a></li>
        <li class="hideOnMobile"><a class="nvlivk" href="home.php">Home</a></li>
        <li class="hideOnMobile"><a class="nvlivk" href="logout.php">Logout</a></li>
        <?php foreach($pictures as $picture):?>
      <li class="hideOnMobile"><?php  echo" <a href='profile.php?id=". $_SESSION['ID'] ."'><img class='default' src='".$picture['default_pic']."'  width='26px'></a>"?>
      &nbsp;<?php echo $picture['user_name']?></li>
      <?php endforeach;?>
      <?php else :?>
        <li class="hideOnMobile"><a class="nvlivk" href="login.php">Login</a></li>
        <li class="hideOnMobile"><a class="nvlivk" href="register.php">Register</a></li>
        
      <?php endif;?>
      <li class="menu-button" onclick=showSidebar()><a class="hov" href="#"><svg xmlns="http://www.w3.org/2000/svg" height="26" viewBox="0 96 960 960" width="26"><path d="M120 816v-60h720v60H120Zm0-210v-60h720v60H120Zm0-210v-60h720v60H120Z"/></svg></a></li>
    </ul>
    <div class="border">
      <div class="modal">
        <ul class="sidebar">
          <li class="bor" onclick=hideSidebar()><svg  xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 30 30" width="30px" height="30px">    <path d="M 7 4 C 6.744125 4 6.4879687 4.0974687 6.2929688 4.2929688 L 4.2929688 6.2929688 C 3.9019687 6.6839688 3.9019687 7.3170313 4.2929688 7.7070312 L 11.585938 15 L 4.2929688 22.292969 C 3.9019687 22.683969 3.9019687 23.317031 4.2929688 23.707031 L 6.2929688 25.707031 C 6.6839688 26.098031 7.3170313 26.098031 7.7070312 25.707031 L 15 18.414062 L 22.292969 25.707031 C 22.682969 26.098031 23.317031 26.098031 23.707031 25.707031 L 25.707031 23.707031 C 26.098031 23.316031 26.098031 22.682969 25.707031 22.292969 L 18.414062 15 L 25.707031 7.7070312 C 26.098031 7.3170312 26.098031 6.6829688 25.707031 6.2929688 L 23.707031 4.2929688 C 23.316031 3.9019687 22.682969 3.9019687 22.292969 4.2929688 L 15 11.585938 L 7.7070312 4.2929688 C 7.5115312 4.0974687 7.255875 4 7 4 z"/></svg></li>
          <?php if(isset($_SESSION['islogged']) ) :?>
          <li><a class="nvlivk" href="create-post.php">create post</a></li>
          <li><a class="nvlivk" href="home.php">Home</a></li>
          <li><a class="nvlivk" href="logout.php">Logout</a></li>
          <li><?php  echo" <a href='profile.php?id=". $_SESSION['ID'] ."'><img class='default' src='".$picture['default_pic']."'  width='26px'>"?>
          <span style="margin-left: 10px;"><?php echo $picture['user_name']?></span></a></li>
          <?php else:?>
          
          <li><a class="nvlivk" href="login.php">Login</a></li>
          <li><a class="nvlivk" href="register.php">Register</a></li>
          <?php endif;?>
        </ul>
      </div>
    </div>
  </nav>       
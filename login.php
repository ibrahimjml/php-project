<?php
session_start();
include_once 'validation.php';
if(isset($_SESSION['islogged'])){
  header("location: home.php");
}
?>
<?php include 'header.php';?>


  <div class="rgstr-form">
    <h2><h2>Login Form</h2></h2>
<?php if(isset($_SESSION['empty'])) {?>
  <p class="error"><?php echo $_SESSION['empty']; ?></p>
  <?php }?>
  <?php unset($_SESSION['empty']);?>
    
    <form method="POST" action="actions/loginaction.php">
      <input type="email" placeholder="Email" name="email">
      <?php if(isset($_SESSION['noeml'])) {?>
      <p style="color:red;margin-bottom:5px;"><?php echo $_SESSION['noeml']; ?></p>
      <?php } ?>
      <?php unset($_SESSION['noeml']);?>
      <input type="password" placeholder="Password" name="pass">
      <input type="submit" value="login">
      <p class="ppp">Don't have an account ?<a href="register.php">register</a></p>



    </form>
  </div>

<?php include 'footer.php';?>
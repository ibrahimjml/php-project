<?php 
session_start();
include_once 'functions/validation.php';

if(isset($_SESSION['islogged'])){
  header("location: home.php");
}


?>

<?php include 'header.php';?>


<!-- session error -->
  
<?php foreach(sessionGet('errors') as $error):?>
  <p class="error" style="text-align: left;margin:7px;width:30%;"><?php echo $error; ?></p>
    <?php endforeach ?>
    <?php removeSession("errors"); ?>

  <div class="rgstr-form">
    <h2>Register Form</h2>
   
      <form method="POST" action="actions/registeraction.php" id="myform">
      
    <div class="parent-user">
            <label for="username">Username</label>
            <input type="text" placeholder="Name" name="username" id="username" value="<?php if(isset($_SESSION['username'])) echo $_SESSION['username'];?>">
            <span class="icon-check-circle"></span>
                  <p style="display:none;color:red;">at least 4 characters. MAX 14</p>
    </div>
  
        
          <div class="parent-user">
              <label for="email">Email</label>
              <input type="text" placeholder="Email" name="email" id="email" value="<?php if(isset($email)) echo $email;?>">
              <span class="icon-check-circle"></span>
              <p style="display:none;color:red;">please enter a correct email address</p>
          </div>
            <div class="parent-user">
              <label for="password">password</label>
              <input type="password" placeholder="Password" name="pass" id="password">
              <span class="icon-check-circle"></span>
              <p  style="display:none;color:red;">password must be 8 characters with 1 upper case letter and 1 number and 1 sympol</p>
            </div>
          <input type="submit" value="Register" id="register">   
          <p class="ppp">allready registered ?<a href="login.php">login in</a></p>
      </form>
  </div>

<?php include 'footer.php';?>

<script>
  const divp1 =document.querySelectorAll(".parent-user p")[0];
const divp2 =document.querySelectorAll(".parent-user p")[1];
const divp3 =document.querySelectorAll(".parent-user p")[2];
const user=document.getElementById("username");
const email=document.getElementById("email");
const pass=document.getElementById("password");



  pass.addEventListener("keyup",() => {

    pass.classList.add("error3");
    divp3.style.display="block";
    const regpass =/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/;
    if(regpass.test(pass.value)){
      
      divp3.style.display="none";
      pass.classList.add("success");
      pass.classList.remove("error3");
      pass.nextElementSibling.style.opacity="1";
    }else{
      divp3.style.display="block";
      pass.classList.remove("success");
      pass.classList.add("error3");
      pass.nextElementSibling.style.opacity="0";
    }
    register();
  }
  )
  
  email.addEventListener("keyup",(eo) => {
    email.classList.add("error3");
    divp2.style.display="block";
    
    const regEmail=/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if(regEmail.test(email.value)){
      email.classList.remove("error3");
      email.classList.add("success");
      divp2.style.display="none";
      email.nextElementSibling.style.opacity="1";
    }else{
      email.classList.add("error3");
      email.classList.remove("success");
      divp2.style.display="block";
      email.nextElementSibling.style.opacity="0";
    }
register();
  }
  );
  
  user.addEventListener("keyup",(eo) => {
    user.classList.add("error3");
    divp1.style.display="block";
  
    if(user.value.length >= 4 && user.value.length < 15 ){
      user.classList.remove("error3");
      user.classList.add("success");
      divp1.style.display="none";
      user.nextElementSibling.style.opacity="1";
    }else{
      user.classList.add("error3");
      user.classList.remove("success");
      divp1.style.display="block";
      user.nextElementSibling.style.opacity="0";
    }
register();
  }
  );
  
// remove "disabled" attribute when all input field success
const regbtn = document.getElementById("register");
regbtn.setAttribute("disabled","");
const register = () => {
  if(user.classList.contains("success") && email.classList.contains("success") && pass.classList.contains("success")){
    regbtn.removeAttribute("disabled"); 
  }else{
     regbtn.setAttribute("disabled","")
  }
}
   

</script>
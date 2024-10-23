<?php 
session_start();

if(!isset($_SESSION['isadmin']) || !$_SESSION['isadmin']){
  die("Access Denied");
}

include_once 'db-connection.php';
$sql = "SELECT  user_id,user_name,user_email FROM users WHERE is_admin = 0";
$result = $conn->query($sql);
$users = [];
if($result->num_rows > 0){
  while($row = $result->fetch_assoc()){
    $users[]=$row;
  }
}

$sql2 = "SELECT  user_id,user_name FROM users WHERE is_admin = 1";
$result = $conn->query($sql2);
$admins = [];
if($result->num_rows > 0){
  while($row = $result->fetch_assoc()){
    $admins[]=$row;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>This Page Only for Admins</h1>
  <h2>All Users</h2>
  
  <table >
    <thead>
      <tr>
      <th>username</th>
      <th>email</th>
      <th>make admin</th>
      </tr>
      
    </thead>
    <tbody>
    <?php foreach($users as $user) :?>
      <tr>
        <td> 
          <?php echo $user["user_name"];?>
        </td>
        <td> 
          <?php echo $user["user_email"];?>
        </td>
        <td> 
          <?php echo "
            <form action='actions/makeadminaction.php' method='POST'>
            <input type='hidden' value='".$user['user_id']."'name='userID'>
            <input type='submit' value='make'>
            </form>
          
          " ?>
        </td>
      </tr>
      <?php endforeach;?>
    </tbody>
    
  </table>
  
  <h2>All Admins </h2>

  <table border="1">
    <thead>
      <tr>
      <th>username</th>
      <th>remove admin</th>
      </tr>
      
    </thead>
    <tbody>
    <?php foreach($admins as $admin) :?>
      <tr>
        <td> 
          <?php echo $admin["user_name"];?>
        </td>
        <td> 
          <?php echo "
            <form action='actions/removeadminaction.php' method='POST'>
            <input type='hidden' value='".$admin['user_id']."'name='userID'>
            <input type='submit' value='remove'>
            </form>
          
          " ?>
        </td>
      </tr>
      <?php endforeach;?>
    </tbody>
    
  </table>
  <button class="btn"><a href="home.php">Go Home</a></button>
  <button class="btn2"><a href="logout.php">Logout</a></button>
</body>
</html>
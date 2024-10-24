<?php 
session_start();
ini_set("display_errors",1);

require 'functions/validation.php';
require_once 'db-connection.php';

if(!isset($_SESSION['islogged']) || !$_SESSION['islogged'] ){
  die("Access Denied");
}



// select username from his id to display in home page
$u = "SELECT user_name FROM users WHERE user_id =".$_SESSION['ID'];
$result_u = $conn->query($u);
$rows = $result_u->fetch_assoc();
$user = $rows['user_name'];

// Fetch users
 $sql = "SELECT user_id, user_name,user_email FROM users";
 $hasusers = true;
 $result = $conn->query($sql);
 if($result->num_rows > 0){
  $users = [];
  while($row = $result->fetch_assoc()){
    $users[]=$row;
  }
  
 }else{
  $hasusers = false;
 }

// follow status
$follow_status = [];
foreach ($users as $user) {
    $query = "SELECT * FROM follows WHERE follower_id = ? AND followed_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['ID'], $user['user_id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $follow_status[$user['user_id']] = mysqli_stmt_num_rows($stmt) > 0;
}

 // fetch from posts username in users and count like in likes innrjoin and left join
 $sql2 = "SELECT p.post_id,p.post_text,p.post_image,p.user_id,p.date_posted,u.user_name,u.default_pic,COUNT(l.like_id) as likes
 FROM posts p
 inner join users u ON u.user_id = p.user_id
 left join likes l ON l.post_id = p.post_id
 GROUP BY p.post_id
  ORDER BY p.date_posted DESC";
 $result2 = $conn->query($sql2);
 if($result2->num_rows > 0){
    $posts = [];
    while($row2 = $result2->fetch_assoc()){
      $posts[]=$row2;
    }
 }


 
 
?>


  <?php include 'header.php';?>
  <!-- session success -->
  <?php foreach(sessionGet('success') as $success):?>
    <p class="success" style="width: 30%;"><?php echo $success; ?></p>
      <?php endforeach ?>
      <?php removeSession("success"); ?>
  <!-- session error -->
  <?php foreach(sessionGet('errors') as $error):?>
    <p class="error" style="width: 30%;"><?php echo $error; ?></p>
      <?php endforeach ?>
      <?php removeSession("errors"); ?>

  <div >
    <form class="searchbar" action="search.php" style="margin:auto;max-width:300px">
    <input type="search" placeholder="Search for users" name="query">
    <button type="submit"><i class="fa fa-search"></i></button>
  
    </form>
  </div>



  <?php if(!empty($posts)):?>
  <?php foreach($posts as $post): ?>
<?php $timestamp = strtotime($post['date_posted']);?>
  <div class="card" style="width: 25rem;height:fit;">
  <?php echo "<div class='wrap'>";?>
  <img src="<?php echo $post['default_pic'] ?>" alt='' width='50px' height="50px" style="border-radius: 100%;">
  <?php echo "<div class='post'>"."<span>".$post['user_name']."</span>" ."<br>". date('d-m-y',$timestamp) ."</div>";?>

  <?php if( $post['user_id'] != $_SESSION['ID']):?>
<?php if($follow_status[$post['user_id']]):?>
  <button class="btn-primary" style="width:100px;height:40px;margin-left:auto;" id="follow-button-<?php echo $post['user_id']; ?>" onclick="Follow(<?php echo $post['user_id']; ?>)">
  Unfollow
  </button>
<?php else:?>
  <button class="btn-primary" style="width:100px;height:40px;margin-left:auto;" id="follow-button-<?php echo $post['user_id']; ?>" onclick="Follow(<?php echo $post['user_id']; ?>)">
  Follow
  </button>
  <?php endif;?>
            <?php endif;?>

  <?php echo "</div>";?>
  <?php if($post['post_image']):?>
  <img  src="<?php echo $post['post_image']; ?>" width="100%" height="300px">
  <?php endif;?>
  <div class="card-body">

    <p class="card-text"><?php echo $post['user_name']." : ".$post['post_text'];?></p>
    
    </div>
  <?php if($post['user_id'] == $_SESSION['ID']):?>
  
    <?php echo "
    <form method='POST' action='actions/deletepostaction.php'>
        <input type='hidden' value='". $post['post_id']."' name='post_id'>
        <input type='submit' class=' btn-primary' value='Delete' onclick='ConfirmDelete()';>
    </form>
    ";
    ?>
    <?php else:?>
      <?php
  
    // Check if the current user has liked the post
    $query = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $post['post_id'], $_SESSION['ID']);
    mysqli_stmt_execute($stmt);
    $liked = mysqli_stmt_get_result($stmt)->num_rows > 0;
    ?>

<span id="like-count-<?php echo $post['post_id']; ?>">&#128420;<?php echo $post['likes']; ?></span>
     <button style="width: 40%;margin-top:5px;border-radius:4px;" class=" btn-danger" id="like-button-<?php echo $post['post_id']; ?>" onclick="Like(<?php echo $post['post_id']; ?>)">
      <?php echo $liked ? "Unlike" : "Like"?>

     <?php endif;?>
                  
        
          
</div>
<?php endforeach;?>
dfdsfdsf
<?php else :?>
  <h1>NO POSTS YET </h1>
<?php endif;?>
    <?php include 'footer.php';?>
  
</body>
</html>




  
    


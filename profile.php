<?php 
session_start();
require 'db-connection.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION['islogged']) || !$_SESSION['islogged'] ){
  die("Access Denied");
}
if(!isset($_GET['id']) || empty(trim($_GET['id']))){
  header("location: profile.php?err1");
}
$userid = $_GET['id'];

$sql = "SELECT user_name,default_pic FROM users WHERE user_id = ?";
$stmt=mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt,"i",$userid);
if(mysqli_stmt_execute($stmt)){
  $results = mysqli_stmt_get_result($stmt);
  if($user = mysqli_fetch_assoc($results)){
    $username= $user['user_name'];
    $default=$user['default_pic'];
  }else{
    die(" no user found");
  }
}

$sql2 = "SELECT p.post_id,p.post_text,p.post_image,p.user_id,p.date_posted,u.user_name FROM posts p
inner join users u ON u.user_id = p.user_id
where u.user_id =?
ORDER BY p.date_posted DESC";

$posts=[];
$stmt =mysqli_prepare($conn,$sql2);
mysqli_stmt_bind_param($stmt,"i",$userid);
if(mysqli_stmt_execute($stmt)){
  $result= mysqli_stmt_get_result($stmt);
  while($post = mysqli_fetch_assoc($result)){
    $posts[]=$post;
  }
}

// get post count 
$sqlpost = "SELECT COUNT(post_id) as postcount FROM posts WHERE user_id = ?";
$stmt_post = mysqli_prepare($conn,$sqlpost);
mysqli_stmt_bind_param($stmt_post,"i",$userid);
mysqli_stmt_execute($stmt_post);
$results =mysqli_stmt_get_result($stmt_post);
$post_count= $results->fetch_assoc()['postcount'];

// get following count 
$sqlfollow = "SELECT COUNT(followed_id) as followingcount FROM follows WHERE follower_id = ?";
$stmt_follow = mysqli_prepare($conn,$sqlfollow);
mysqli_stmt_bind_param($stmt_follow,"i",$userid);
mysqli_stmt_execute($stmt_follow);
$results =mysqli_stmt_get_result($stmt_follow);
$followingcount= $results->fetch_assoc()['followingcount'];

// get followers count 
$sqlfollower = "SELECT COUNT(follower_id) as followercount FROM follows WHERE followed_id = ?";
$stmt_follower = mysqli_prepare($conn,$sqlfollower);
mysqli_stmt_bind_param($stmt_follower,"i",$userid);
mysqli_stmt_execute($stmt_follower);
$results =mysqli_stmt_get_result($stmt_follower);
$followercount= $results->fetch_assoc()['followercount'];

//fetch follow status
$query = "SELECT * FROM follows WHERE follower_id = ? AND followed_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['ID'], $userid);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $follow_status[$userid] = mysqli_stmt_num_rows($stmt) > 0;
    ?>

<?php include 'header.php';?>

  
  
  <img class="img1" src="<?php echo $default?>" alt="" width="150px">

  <h1><?php echo $username;?> </h1>
  <?php if($userid != $_SESSION['ID'] ) :?>
  <button class="btn-primary" style="margin-top:30px;display:block;width:120px;margin-left:auto;margin-right:auto;padding:5px;border-radius:7px;" id="follow-button-<?php echo $userid ?>" onclick="Follow(<?php echo $userid; ?>)">
  <?php echo $follow_status[$userid] ? 'Unfollow' : 'Follow'; ?>
  </button>
  <?php endif;?>
  <?php if($userid == $_SESSION['ID'] ) :?>
  
    <a href="editprofile.php" class="btn-primary" style="text-align:center;color: white;font-size:16px;text-decoration:none;display:block;width:120px;margin-left:auto;margin-right:auto;padding:5px;border-radius:7px;">Edit</a>

  
  <?php endif;?>

  <div style="display: flex;margin-top:40px;justify-content:center;gap:30px;">

    <div style="display: flex;flex-direction:column;">
      <p style="font-size: large;font-weight:bold;">Posts</p>
    
        <p style="font-weight: 600;font-size:medium;text-align:center;"><?php echo $post_count?></p>
    
      </div>
      
      <div style="display: flex;flex-direction:column;">
        <p style="font-size: large;font-weight:bold;">followers</p>
        
        <p style="font-weight: 600;font-size:medium;text-align:center;"><?php echo $followercount?></p>
      </div>
      <div style="display: flex;flex-direction:column;">
      
            <p style="font-size: large;font-weight:bold;">following</p>
              
            <p style="font-weight: 600;font-size:medium;text-align:center;"><?php echo $followingcount?></p>
      </div>
  </div>
<hr >
  <!-- card bootstrap -->
  
  <div style='display:flex;flex-wrap:wrap;justify-content:center;gap:25px;'>
  <?php
foreach($posts as $post){ ?>

  <div class="card-profile"  style="width: 15rem; border:none;">

  

  
  <?php if($post['post_image']){?>
  <img class="card-img-top" src="<?php echo $post['post_image']; ?>" width="300px" height="200px">
  <?php }?>

  <div class="card-body"  >
    
  
  <div style="display: flex;gap:10px;">
  <?php 
  if($post['user_id'] == $_SESSION['ID']){?>
    <?php echo "
  
    <form method='POST' action='actions/deletepostaction.php'>
        <input type='hidden' value='". $post['post_id']."' name='post_id'>
        <input type='submit' class=' btn-danger' style='width:80px;' value='Delete'>
    </form>
    <form method='GET' action='editpost.php'>
      <input type='hidden' value='". $post['post_id']."' name='post_id'>
      <input type='submit' class=' btn-primary' style='width:80px;' value='Edit'>
  </form>
  

  ";?>
  <?php }?>
  </div>
</div>
</div>
<?php }?>
</div>
  

            

  <!-- card bootstrap -->
   
<?php include 'footer.php';?>
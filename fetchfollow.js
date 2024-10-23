
 function Follow(userId) {
  
  const Options = {
    method: "POST",
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      
    body: new URLSearchParams({
      followed_id: userId
  })
      };

      fetch('apis/followaction.php', Options) 
        
      .then((response) => response.json())
      .then((data)=>followDone(data,userId))
      .catch((error) => console.error(error));
     }
      

  function followDone(data,userId){

    if (data.error) {
        console.error('Error:', data.error);
        return;
    }
  
    const followButton = document.querySelector(`#follow-button-${userId}`);
  
    followButton.innerText = data.followed ? 'Unfollow' : 'Follow';
  }



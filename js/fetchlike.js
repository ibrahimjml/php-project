function Like(postId) {
  const Options = {
          method: "POST",
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            
          body: new URLSearchParams({
                  post_id: postId
              })
            };
           fetch('actions/likepostaction.php', Options) 
            
          
          

           .then((response) => response.json())
           .then((data)=>likeDone(data,postId))
           .catch((error) => console.error(error));
          }
          
          function likeDone(data,postId){

        if (data.error) {
          console.error('Error:', data.error);
          return;
      }
          const likeButton = document.querySelector(`#like-button-${postId}`);
          const likeCount = document.querySelector(`#like-count-${postId}`);
          
  
        likeButton.innerText = data.liked ? 'Unlike' : 'Like';
        likeCount.innerHTML = `&#128420;${data.like_count}`;
          
        }
      
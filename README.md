## About PHP project

![schooldash-dahboard-page](https://i.postimg.cc/pX0prkMM/Screenshot-2024-10-23-141946.png)
![schooldash-dahboard-page](https://i.postimg.cc/nhjczJ1q/Screenshot-2024-10-23-150732.png)



## PHP RESTful API for this project 
-make sure to put in headers Content-Type:application/json & Authorization.

`Login`
- `POST /apis/login.php` - login to get token access.
- `POST /apis/logout.php` - logout delete token, token required.

 `Posts`

- `GET  /apis/getallposts.php` - Retrieve all posts , no authentication required.
- `GET  /apis/getpost.php` - get single post , no authentication required.
- `POST /apis/createpost.php` - Create new post , token required.
- `POST /apis/updatepost.php` - Update authorized post , token required.
- `DELETE /apis/destroypost.php` - Delete authorized post , token required.

### Features

#### User Authentication/Authorization and profile Management
- **User Registration and Login:** implemented Secure user  register/login .
- **Profile Management:** Users can view their profiles including posts,number of posts,total likes,followers/following, and edit profile settings name ,email, change password only authorized can access it, including changing their profile image,update and delete current one.

#### Blog Posts
- **Create, Read, Update, and Delete (CRUD) Operations:** Users can create new blog post, edit existing one, and delete post they have authored.
- **Like/unlike:** Implement Like/Unkike button using fetchApi with Like animation falling hearts for better user experience. 
- **Follow/unfollow:** Implement Like/Unkike button using fetchApi with Like animation falling hearts for better user experience. 
- **Search :** user can search for availablw users .
#### Installation
- **Create table example-php then import sql file example-php.sql .

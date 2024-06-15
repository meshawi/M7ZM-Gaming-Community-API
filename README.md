<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


# M7ZM-Gaming-Community API

Welcome to the backend API for the **M7ZM-Gaming-Community** project! This API handles all the functionalities required to run the gaming community platform, from user management to media uploads and interactions. This project is designed to provide a seamless experience for gamers to share their content and interact with each other.

🔗 **Project Repository:** [M7ZM-Gaming-Community](https://github.com/yourusername/m7zm-gaming-community)

## Table of Contents 📚

- [Overview](#overview)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database](#database)
- [Folder Structure](#folder-structure)
- [API Endpoints](#api-endpoints)
  - [Authentication](#authentication)
  - [User Management](#user-management)
  - [Media Management](#media-management)
  - [Admin Operations](#admin-operations)
  - [Reactions and Comments](#reactions-and-comments)
- [Models](#models)
- [Controllers](#controllers)
- [Error Handling](#error-handling)
- [Credits and Disclaimer](#credits-and-disclaimer)

## Overview 🌟

The **M7ZM-Gaming-Community API** is built using Laravel and is designed to manage users, media content, reactions, comments, and administrative tasks for the gaming community platform. It provides a robust backend to handle various types of media, including videos and images, and supports functionalities like tagging, liking, commenting, and more.

## Requirements 🛠️

- PHP >= 7.4
- Composer
- MySQL or any other database supported by Laravel
- Laravel 8 or above

## Installation 📥

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/m7zm-gaming-community.git
   cd m7zm-gaming-community
   ```
2. **Install dependencies:**
   ```bash
    composer install
   ```

3. **Set up environment variables:**
   ```bash
   cp .env.example .env
   ```
4. **Generate application key:**
   ```bash
   php artisan key:generate
   ```
5. **Run migrations:**
   ```bash
   php artisan migrate
   ```
6. **Seed the database (optional):**
   ```bash
    php artisan db:seed
   ```
7. **Run the server:**
   ```bash
   php artisan serve
   ```

## Configuration ⚙️
Ensure the .env file is correctly configured for your database and other services:

**.env**
   ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_username
    DB_PASSWORD=your_database_password

    # Add other necessary configurations
   ```

## Database 🗄️
This project uses a MySQL database with the following schema:

**SQL**
   ```sql
    CREATE TABLE m7zm_users (
    user_id int(11) NOT NULL AUTO_INCREMENT,
    username varchar(100) NOT NULL,
    password varchar(255) NOT NULL,
    full_name varchar(255) NOT NULL,
    bio text DEFAULT NULL,
    profile_picture varchar(255) DEFAULT 'default_profile_picture.jpg',
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NULL DEFAULT NULL,
    last_login timestamp NULL DEFAULT NULL,
    status enum('active', 'inactive', 'banned') DEFAULT 'active',
    profile_visibility enum('public', 'private') DEFAULT 'public',
    discord_role varchar(100) DEFAULT NULL,
    user_prefer_url varchar(255) DEFAULT NULL,
    authorization_level enum('ADMIN', 'Moderator', 'User') DEFAULT 'User',
    accounts_ids longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(accounts_ids)),
    login_history longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(login_history)),
    PRIMARY KEY (user_id),
    UNIQUE KEY username (username)
    );

    CREATE TABLE games (
        game_id INT AUTO_INCREMENT PRIMARY KEY,
        game_name VARCHAR(255) NOT NULL,
        game_details TEXT,
        thumbnail VARCHAR(255) 
    );

    CREATE TABLE user_favorite_games (
        user_id INT,
        game_id INT,
        rank INT,
        PRIMARY KEY (user_id, rank),
        FOREIGN KEY (user_id) REFERENCES m7zm_users(user_id),
        FOREIGN KEY (game_id) REFERENCES games(game_id)
    );

    CREATE TABLE user_games_achieved (
        user_id INT,
        game_id INT,
        PRIMARY KEY (user_id, game_id),
        FOREIGN KEY (user_id) REFERENCES m7zm_users(user_id),
        FOREIGN KEY (game_id) REFERENCES games(game_id)
    );

    CREATE TABLE videos (
        video_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        video_path VARCHAR(255) NOT NULL,
        thumbnail_path VARCHAR(255) DEFAULT 'default_thumbnail.jpg',
        visibility ENUM('open', 'public', 'archived') DEFAULT 'public',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES m7zm_users(user_id)
    );

    CREATE TABLE tags (
        tag_id INT AUTO_INCREMENT PRIMARY KEY,
        tag_name VARCHAR(100) NOT NULL UNIQUE
    );

    CREATE TABLE video_tags (
        video_id INT,
        tag_id INT,
        PRIMARY KEY (video_id, tag_id),
        FOREIGN KEY (video_id) REFERENCES videos(video_id),
        FOREIGN KEY (tag_id) REFERENCES tags(tag_id)
    );

    CREATE TABLE video_reactions (
        reaction_id INT AUTO_INCREMENT PRIMARY KEY,
        video_id INT NOT NULL,
        user_id INT NOT NULL,
        reaction_type ENUM('like', 'dislike') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (video_id) REFERENCES videos(video_id),
        FOREIGN KEY (user_id) REFERENCES m7zm_users(user_id),
        UNIQUE (video_id, user_id)
    );

    CREATE TABLE comments (
        comment_id INT AUTO_INCREMENT PRIMARY KEY,
        video_id INT NOT NULL,
        user_id INT NOT NULL,
        comment_text TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (video_id) REFERENCES videos(video_id),
        FOREIGN KEY (user_id) REFERENCES m7zm_users(user_id)
    );

    CREATE TABLE user_favorite_videos (
        user_id INT,
        video_id INT,
        PRIMARY KEY (user_id, video_id),
        FOREIGN KEY (user_id) REFERENCES m7zm_users(user_id),
        FOREIGN KEY (video_id) REFERENCES videos(video_id)
    );

    CREATE TABLE images (
        image_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        image_path VARCHAR(255) NOT NULL,
        visibility ENUM('open', 'public', 'archived') DEFAULT 'public',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES m7zm_users(user_id)
    );

    CREATE TABLE image_tags (
        image_id INT,
        tag_id INT,
        PRIMARY KEY (image_id, tag_id),
        FOREIGN KEY (image_id) REFERENCES images(image_id),
        FOREIGN KEY (tag_id) REFERENCES tags(tag_id)
    );

    CREATE TABLE image_reactions (
        reaction_id INT AUTO_INCREMENT PRIMARY KEY,
        image_id INT NOT NULL,
        user_id INT NOT NULL,
        reaction_type ENUM('like', 'dislike') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (image_id) REFERENCES images(image_id),
        FOREIGN KEY (user_id) REFERENCES m7zm_users(user_id),
        UNIQUE (image_id, user_id)
    );

    CREATE TABLE image_comments (
        comment_id INT AUTO_INCREMENT PRIMARY KEY,
        image_id INT NOT NULL,
        user_id INT NOT NULL,
        comment_text TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (image_id) REFERENCES images(image_id),
        FOREIGN KEY (user_id) REFERENCES m7zm_users(user_id)
    );
   ```
## Folder Structure 📂
   ```plaintext
   api
├── app
│   ├── Http
│   │   ├── Controllers
│   │   │   ├── Api
│   │   │   │   ├── AdminMediaController.php
│   │   │   │   ├── AdminUserController.php
│   │   │   │   ├── ApiController.php
│   │   │   │   ├── CodController.php
│   │   │   │   ├── GameController.php
│   │   │   │   ├── GameInstructionsController.php
│   │   │   │   ├── M7ZMUserController.php
│   │   │   │   ├── MediaController.php
│   │   │   │   ├── SDCardController.php
│   │   │   │   ├── WorkshopController.php
│   │   │   ├── AllEditsController.php
│   │   │   ├── CommentController.php
│   │   │   ├── FavoriteController.php
│   │   │   ├── ImageUploadController.php
│   │   │   ├── TagController.php
│   │   │   ├── VideoReactionController.php
│   │   │   ├── VideoUploadController.php
│   │   ├── Middleware
│   │   │   ├── ...
│   │   ├── Resources
│   │   │   ├── ...
│   ├── Models
│   │   ├── Comment.php
│   │   ├── Game.php
│   │   ├── Image.php
│   │   ├── ImageComment.php
│   │   ├── ImageReaction.php
│   │   ├── ImageTag.php
│   │   ├── M7ZMUser.php
│   │   ├── Tag.php
│   │   ├── UserFavoriteGame.php
│   │   ├── UserFavoriteVideo.php
│   │   ├── UserGameAchieved.php
│   │   ├── Video.php
│   │   ├── VideoReaction.php
│   │   ├── VideoTag.php
├── bootstrap
│   ├── ...
├── config
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── sanctum.php
│   ├── services.php
│   ├── session.php
├── database
│   ├── factories
│   │   ├── UserFactory.php
│   ├── migrations
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2024_05_27_001243_create_personal_access_tokens_table.php
│   ├── seeders
│   │   ├── DatabaseSeeder.php
├── public
│   ├── codeImages
│   ├── SDImages
│   ├── storage
│   ├── .htaccess
│   ├── favicon.ico
│   ├── index.php
│   ├── robots.txt
├── resources
│   ├── css
│   │   ├── app.css
│   ├── js
│   │   ├── app.js
│   │   ├── bootstrap.js
│   ├── views
│   │   ├── welcome.blade.php
├── routes
│   ├── api.php
│   ├── console.php
│   ├── web.php
├── storage
│   ├── app
│   │   ├── public
│   │       ├── game_thumbnails
│   │       ├── images
│   │       ├── profile_pictures
│   │       ├── videos
│   │       ├── video_thumbnails
│   ├── framework
│   │   ├── cache
│   │   ├── sessions
│   │   ├── testing
│   │   ├── views
│   ├── logs
├── tests
│   ├── ...
├── .editorconfig
├── .env
├── .env.example
├── .gitattributes
├── .gitignore
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── phpunit.xml
├── README.md
├── vite.config.js

   ```

## API Endpoints 🚀

### Authentication 🔒

- **Register:** `POST /register`
- **Login:** `POST /login`
- **Logout:** `GET /logout` (Requires authentication)

### User Management 👥

- **Get Profile:** `GET /profile` (Requires authentication)
- **Get User Details by Username:** `GET /m7zm_user/{username}`
- **Update Username and Password:** `PUT /m7zm_user/update-username-password/{user_id}`
- **Update Full Name, Bio, and Visibility:** `PUT /m7zm_user/update-fullname-bio-visibility/{user_id}`
- **Update Profile Picture:** `POST /m7zm_user/update-profile-picture/{user_id}`
- **Update Account IDs:** `PUT /m7zm_user/update-account-ids/{user_id}`
- **Update Achieved Games:** `PUT /m7zm_user/update-achieved-games/{user_id}`
- **Update Favorite Games:** `PUT /m7zm_user/update-favorite-games/{user_id}`
- **Get All Users:** `GET /m7zm_users`
- **Login (M7ZM User):** `POST /m7zm-login`
- **Register (M7ZM User):** `POST /m7zm-register`

### Media Management 🎥📸

- **Upload Video:** `POST /upload-video`
- **Upload Image:** `POST /upload-image`
- **Edit Video:** `PUT /edit-video/{video_id}`
- **Edit Image:** `PUT /edit-image/{image_id}`
- **Delete Image:** `DELETE /delete-image/{image_id}`
- **Delete Video:** `DELETE /delete-video/{video_id}`
- **Get Videos with Visibility 'Open' or 'Public':** `GET /user/{username}/videos/open-public`
- **Get Images with Visibility 'Open' or 'Public':** `GET /user/{username}/images/open-public`
- **Get All Favorite Videos for the User:** `GET /user/{username}/favorite-videos`
- **Get All Archived Media for the User:** `GET /user/{username}/archived-media`
- **Get All Videos by Username:** `GET /all-videos/{username}`
- **Get All Images by Username:** `GET /all-images/{username}`
- **Get All Public Videos:** `GET /all-public-videos`
- **Get All Public Images:** `GET /all-public-images`
- **Get Video Details:** `GET /video-details/{video_id}`
- **Get All Tags:** `GET /tags`

### Reactions and Comments ❤️💬

- **React to Video:** `POST /video/{video_id}/react`
- **Update Reaction to Video:** `POST /video/{video_id}/update-reaction`
- **Check User Reaction to Video:** `GET /video/{video_id}/reaction/{user_id}`
- **Add Comment to Video:** `POST /video/{video_id}/comment`
- **Get Comments for Video:** `GET /video/{video_id}/comments`
- **Edit Comment:** `PUT /comment/{comment_id}`
- **Delete Comment:** `DELETE /comment/{comment_id}`
- **Check Favorite:** `GET /video/{video_id}/favorite/{user_id}`
- **Add Favorite:** `POST /video/{video_id}/favorite`
- **Remove Favorite:** `DELETE /video/{video_id}/favorite/{user_id}`

### Admin Operations 🛠️

- **Edit User Details:** `PUT /admin/edit-user/{user_id}`
- **Delete User:** `DELETE /admin/delete-user/{user_id}`
- **Get All Videos (Admin):** `GET /admin/videos`
- **Get All Images (Admin):** `GET /admin/images`

### COD, Workshop, and SDCard Games 🎮

- **Get COD Games:** `GET /cod`
- **Get Workshop Games:** `GET /workshop-games`
- **Get SD Cards:** `GET /sd-cards`
- **Get SD Instructions:** `GET /sd-instructions`
- **Get All Games:** `GET /games`



## Models 🧩

- **M7ZMUser**: Represents the user model.
- **Video**: Represents the video model.
- **Image**: Represents the image model.
- **Tag**: Represents the tag model.
- **VideoReaction**: Represents the video reaction model.
- **Comment**: Represents the comment model.
- **UserFavoriteVideo**: Represents the user favorite video model.
- **Game**: Represents the game model.
- **ImageTag**: Represents the image tag model.
- **VideoTag**: Represents the video tag model.

## Controllers 🎮

- **ApiController**: Handles user registration, login, and profile management.
- **M7ZMUserController**: Manages M7ZMUser-specific operations.
- **MediaController**: Manages media-related operations such as getting videos and images.
- **VideoUploadController**: Handles video uploads.
- **ImageUploadController**: Handles image uploads.
- **VideoReactionController**: Manages reactions to videos.
- **CommentController**: Manages comments on videos.
- **FavoriteController**: Manages user favorite videos.
- **AdminUserController**: Handles administrative operations for users.
- **AdminMediaController**: Handles administrative operations for media content.
- **CodController**: Manages COD game-related data.
- **GameController**: Manages game-related data.
- **GameInstructionsController**: Manages game instructions.
- **SDCardController**: Manages SD card-related data.
- **WorkshopController**: Manages workshop games.

## Error Handling ⚠️

The API uses standard HTTP status codes to indicate the success or failure of an API request. The responses are formatted as JSON objects containing a `status` and a `message` field, and optionally additional data or error details.

## Credits and Disclaimer 📢
**Credits**: This project is developed by **Mohammed Aleshawi**. All credits go to him and only him.

**Disclaimer**: This project is for learning purposes only. Mohammed Aleshawi is not responsible for any misuse of this project. No one is allowed to use this website without his explicit consent.
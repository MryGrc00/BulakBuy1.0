<?php
session_start();
include '../php/dbhelper.php';

$pdo = dbconnect();

if (isset($_GET['user_id'])) {
    $userID = $_GET['user_id'];
    $users = get_record('users', 'user_id', $userID);

    // Check if the user exists
    if ($users) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $firstname = htmlspecialchars($_POST['first_name']);
            $lastname = htmlspecialchars($_POST['last_name']);
            $username = htmlspecialchars($_POST['username']);
            $email = htmlspecialchars($_POST['email']);
            $phone = htmlspecialchars($_POST['phone']);
            $zipcode = htmlspecialchars($_POST['zipcode']);
            $address = htmlspecialchars($_POST['address']);

            $imagePath = ''; // Single image path
            $uploadFolder = '../php/images/';

            if (!empty($_FILES['profile_img']['name'])) {
                $file_name = $_FILES['profile_img']['name'];
                $file_tmp = $_FILES['profile_img']['tmp_name'];

                if (!empty($file_name)) {
                    $newFilePath = $uploadFolder . basename($file_name);
                    if (move_uploaded_file($file_tmp, $newFilePath)) {
                        $imagePath = $newFilePath;
                    } else {
                        echo "Error uploading file.";
                        exit();
                    }
                }
            } else {
                $imagePath = $users['profile_img']; // Use the existing image path if no new image is uploaded
            }

            try {
                // Assuming $pdo is initialized in dbhelper.php
                $stmt = $pdo->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, username = :username, email = :email, phone = :phone, zipcode = :zipcode, address = :address, profile_img = :profile_img WHERE user_id = :user_id");
                $stmt->bindParam(':first_name', $firstname);
                $stmt->bindParam(':last_name', $lastname);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':zipcode', $zipcode);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':profile_img', $imagePath);
                $stmt->bindParam(':user_id', $userID);
                $stmt->execute();

                header("Location: vendor_home.php");
                exit();
            } catch (PDOException $e) {
                echo "Error updating user record: " . $e->getMessage();
            }
        }
    } else {
        // Handle the case when the user is not found
        echo "User not found!";
    }
}
?>






<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-pzjw8f+uaex3+ihrbIk8mz07tb2F4F5ssx6kl5v5PmUGp1ELjF8j5+zM1a7z5t2N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/edit_profile.css">
    <link rel="stylesheet" href="../../css/addproduct.css">

   

</head>

<body>
  <header>
    <nav class="navbar navbar-expand-lg">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="#">
          <img src="../../images/logo.png" alt="BulakBuy Logo" class="img-fluid logo">
      </a>
        <!-- Search Bar -->
        <div class="navbar-collapse justify-content-md-center">
            <ul class="navbar-nav dib">
                <li class="nav-item">
                    <form class="form-inline my-2 my-lg-0">
                        <a href=""><i class="fa fa-search"></i></a>
                        <input type="text"  class="form-control form-input" placeholder="Search">
                        <a href="javascript:void(0);" onclick="goBack()">
                          <i class="back fa fa-angle-left" aria-hidden="true"></i>
                          <div id="search-results">Edit Profile</div>
                        </a>
                    </form>
                </li>     
            </ul>
        </div>
    </nav><hr class="nav-hr">
</header>
<div class="wrapper">
    <form action="" enctype="multipart/form-data" method="post">
        <?php if ($users): ?>
            <div class="form-container">
            <h3 class="profimgT">Profile Image</h3>                      
            <div class="image-upload-container">
            <div class="image-upload">
                    <input type="file" name ="profile_img" id="profile_img" class="image-input" accept="image/*" multiple > 
                    <label for="profile_img"><p class="plus"></p class="add-plus"> + Edit Image</label>
                </div>
                <div class="image-preview" id="imagePreview">
                <?php
                        echo '<img src="' . $users['profile_img'] . '" alt="' . $users['last_name'] . '" class="preview-image">';
                    ?>
                </div>
            </div> 
            
    
            <h3 class="prodnaT">First Name</h3>
            <input type="text" name="first_name" id="first_name" value="<?php echo $users['first_name']; ?>" required>
             
            <h3 class="prodnaT">Last Name</h3>
            <input type="text" name="last_name" id="last_name" value="<?php echo $users['last_name']; ?>" required>
             
            <h3 class="prodnaT">Username</h3>
            <input type="text" name="username" id="username" value="<?php echo $users['username']; ?>" required>
             
            <h3 class="prodnaT">Email</h3>
            <input type="text" name="email" id="email" value="<?php echo $users['email']; ?>" required>



            <div class="form-row">
                <div class="container">
                    <h3 class="">Number</h3>
                    <input id="" name="phone" required="" type="text" value="<?php echo $users['phone']; ?>">
                </div>
                <div class="container">
                    <h3 class="">Zipcode</h3>
                    <input id="" name="zipcode" required="" type="text" value="<?php echo $users['zipcode']; ?>">
                </div>
            </div>
            <h3 class="">Address</h3>
            <input id="" name="address" required="" type="text" value="<?php echo $users['address']; ?>">

            <div class="submit-btn">
                <button class="addbtn" type="submit" value="Save">Save</button>
            </div>
        </div>
        <?php else: ?>
            <p>User not found.</p>
        <?php endif; ?>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js">
</script> 
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js">
</script> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js">
</script> 
<script>
        document.addEventListener("DOMContentLoaded", function () {
        var imageInputs = document.querySelectorAll(".image-input");
        var imagePreview = document.getElementById("imagePreview");

        function displayImages() {
            imagePreview.innerHTML = "";
            imageInputs.forEach(function (input) {
                for (var i = 0; i < input.files.length; i++) {
                    var file = input.files[i];
                    if (file.type.startsWith("image/")) {
                        var imageUrl = URL.createObjectURL(file);
                        var imageContainer = document.createElement("div");
                        imageContainer.classList.add("image-container");

                        var imageElement = document.createElement("img");
                        imageElement.classList.add("preview-image");
                        imageElement.src = imageUrl;

                        imageContainer.appendChild(imageElement);
                        imagePreview.appendChild(imageContainer);
                    }
                }
            });
        }

        // Add event listeners to all file inputs
        imageInputs.forEach(function (input) {
            input.addEventListener("change", function () {
                displayImages();
            });
        });
    });


       </script>
         <script>
            function goBack() {
                window.history.back();
            }
          </script>
   
</body>
</html>
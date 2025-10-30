<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$query = $conn->prepare("SELECT name, email, user_type, profile_pic FROM users WHERE id=?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Profile | ExplorIndia</title>
<style>
body {
    font-family: Poppins, sans-serif;
    background: #f4f6f9;
    margin: 0;
}
.header {
    background: #0078ff;
    color: #fff;
    padding: 15px;
    text-align: center;
    font-size: 22px;
    font-weight: 600;
}
.container {
    width: 90%;
    max-width: 1100px;
    margin: 30px auto;
    display: flex;
    gap: 20px;
}
.sidebar {
    width: 260px;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    text-align: center;
}
.profile-pic {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #0078ff;
}
.sidebar h3 { margin-bottom: 5px; }
.sidebar p { color: #666; font-size: 14px; }
.upload-form input { display: none; }
.upload-label {
    display: block;
    background: #0078ff;
    color: #fff;
    padding: 8px;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
    font-size: 14px;
}
.logout-btn {
    background: red;
    color: white;
    padding: 8px 15px;
    display: block;
    margin-top: 20px;
    border-radius: 6px;
    text-decoration: none;
}
.content {
    flex: 1;
}
.card {
    background: white;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.card h2 { margin-top: 0; }
.details p { margin: 6px 0; }
.btn-edit {
    background: #0078ff;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
}
.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px,1fr));
    gap: 15px;
}
.stat-box {
    background: #0078ff;
    color: white;
    text-align: center;
    padding: 18px;
    border-radius: 10px;
}
</style>
</head>
<body>
<div class="header">My Profile</div>
<div class="container">

<div class="sidebar">
    <img src="img/users/<?php echo $user['profile_pic'] ?? 'default.png'; ?>" class="profile-pic">
    <h3><?php echo $user['name']; ?></h3>
    <p><?php echo $user['email']; ?></p>
    <p>Account: <b><?php echo ucfirst($user['user_type']); ?></b></p>

    <form class="upload-form" action="upload_profile.php" method="POST" enctype="multipart/form-data">
        <label class="upload-label" for="profile_pic">Upload Photo</label>
        <input type="file" name="profile_pic" id="profile_pic" onchange="this.form.submit()">
    </form>

    <a class="logout-btn" href="logout.php">Logout</a>
</div>

<div class="content">
    <div class="card">
        <h2>Personal Information</h2>
        <div class="details">
            <p><b>Name:</b> <?php echo $user['name']; ?></p>
            <p><b>Email:</b> <?php echo $user['email']; ?></p>
            <p><b>Account Type:</b> <?php echo ucfirst($user['user_type']); ?></p>
        </div>
        <a class="btn-edit" href="edit_profile.php">Edit Profile</a>
    </div>

    <div class="card">
        <h2>Travel Dashboard</h2>
        <div class="stats">
            <div class="stat-box">Saved Places<br><b>0</b></div>
            <div class="stat-box">Trips<br><b>0</b></div>
            <div class="stat-box">Reviews<br><b>0</b></div>
            <div class="stat-box">Wishlist<br><b>0</b></div>
        </div>
    </div>
</div>

</div>
</body>
</html>

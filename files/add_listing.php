<?php
session_start();
include 'db.php';

/* 🔐 Login check */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title          = $_POST['title'];
    $business_name  = $_POST['business_name'];
    $short_desc     = $_POST['short_desc'];
    $description    = $_POST['description'];
    $category       = $_POST['category'];
    $address        = $_POST['address'];
    $city           = $_POST['city'];
    $state          = $_POST['state'];
    $pincode        = $_POST['pincode'];

    // ✅ optional numeric fields
    $latitude  = ($_POST['latitude'] !== '') ? floatval($_POST['latitude']) : null;
    $longitude = ($_POST['longitude'] !== '') ? floatval($_POST['longitude']) : null;

    $phone       = $_POST['phone'];
    $whatsapp    = $_POST['whatsapp'];
    $email       = $_POST['email'];
    $price_range = $_POST['price_range'];
    $services    = $_POST['services'];

    $owner_id = $_SESSION['user_id'];
    $cover_image = '';

    /* 🖼️ Cover image upload */
    if (!empty($_FILES['cover_image']['name'])) {

        $folder = "img/listings/covers/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid("listing_") . '.' . $ext;
        $path = $folder . $file_name;

        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $path)) {
            $cover_image = $file_name;
        }
    }

    /* 📥 Insert into DB */
    $stmt = $conn->prepare("
        INSERT INTO listings 
        (title, business_name, short_desc, description, category, address, city, state, pincode,
         latitude, longitude, owner_id, phone, whatsapp, email, cover_image, price_range, services,
         status, is_active, views, created_at)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,
                'pending',1,0,NOW())
    ");

    $stmt->bind_param(
        "ssssssssddissssss",
        $title,
        $business_name,
        $short_desc,
        $description,
        $category,
        $address,
        $city,
        $state,
        $pincode,
        $latitude,
        $longitude,
        $owner_id,
        $phone,
        $whatsapp,
        $email,
        $cover_image,
        $price_range,
        $services
    );

    if ($stmt->execute()) {
        $message = "✅ Listing submitted successfully! Approval pending.";
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Business Listing</title>
<style>
body{font-family:Arial;background:#f4f6fa;}
form{background:#fff;width:520px;margin:40px auto;padding:25px;border-radius:10px}
input,textarea,select{width:100%;padding:10px;margin:8px 0}
button{background:#0078ff;color:#fff;padding:10px;border:none;width:100%;cursor:pointer}
button:hover{background:#005fd1}
.msg{text-align:center;margin-top:10px;color:green}
</style>
</head>
<body>

<form method="POST" enctype="multipart/form-data">
<h2>Add Business Listing</h2>

<input name="title" placeholder="Listing Title" required>
<input name="business_name" placeholder="Business Name" required>
<input name="short_desc" placeholder="Short Description" required>

<textarea name="description" placeholder="Full Description" required></textarea>

<input name="category" placeholder="Category (Cafe, Hotel etc)" required>
<input name="address" placeholder="Address" required>
<input name="city" placeholder="City" required>
<input name="state" placeholder="State" required>
<input name="pincode" placeholder="Pincode" required>

<input name="latitude" placeholder="Latitude (optional)">
<input name="longitude" placeholder="Longitude (optional)">

<input name="phone" placeholder="Phone Number">
<input name="whatsapp" placeholder="WhatsApp Number">
<input name="email" placeholder="Business Email">

<select name="price_range">
    <option value="">Price Range</option>
    <option value="₹">₹</option>
    <option value="₹₹">₹₹</option>
    <option value="₹₹₹">₹₹₹</option>
</select>

<textarea name="services" placeholder="Services / Facilities"></textarea>

<input type="file" name="cover_image" accept="image/*">

<button type="submit">Submit Listing</button>

<div class="msg"><?= $message ?></div>
</form>

</body>
</html>

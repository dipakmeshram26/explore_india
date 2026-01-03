<?php
session_start();
include 'db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userName  = $isLoggedIn ? $_SESSION['user_name'] : '';
$userType  = $isLoggedIn ? $_SESSION['user_type'] : '';


/* 🔐 Login check */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title         = $_POST['title'];
    $business_name = $_POST['business_name'];
    $short_desc    = $_POST['short_desc'];
    $description   = $_POST['description'];
    $category      = $_POST['category'];
    $address       = $_POST['address'];
    $city          = $_POST['city'];
    $state         = $_POST['state'];
    $pincode       = $_POST['pincode'];

    // optional numeric values
    $latitude  = ($_POST['latitude'] !== '') ? (float)$_POST['latitude'] : NULL;
    $longitude = ($_POST['longitude'] !== '') ? (float)$_POST['longitude'] : NULL;

    $phone       = $_POST['phone'];
    $whatsapp    = $_POST['whatsapp'];
    $email       = $_POST['email'];
    $price_range = $_POST['price_range'];
    $services    = $_POST['services'];

    $owner_id = $_SESSION['user_id'];
    $cover_image = NULL;

    /* 🖼️ Cover Image Upload */
    if (!empty($_FILES['cover_image']['name'])) {

        $folder = "img/listings/covers/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
        $cover_image = uniqid("listing_") . "." . $ext;

        move_uploaded_file(
            $_FILES['cover_image']['tmp_name'],
            $folder . $cover_image
        );
    }

    /* 📥 Insert Query (DIRECT APPROVED) */
    $stmt = $conn->prepare("
        INSERT INTO listings 
        (title, business_name, short_desc, description, category, address, city, state, pincode,
         latitude, longitude, owner_id, phone, whatsapp, email, cover_image, price_range, services,
         status, is_active, views, created_at)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,
                'approved',1,0,NOW())
    ");

    /* ✅ bind_param FIXED (18 params = 18 types) */
    $stmt->bind_param(
        "sssssssssddissssss",
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
        header("Location: index.php?listing=success");
        exit;
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
form{background:#fff;width:500px;margin:40px auto;padding:25px;border-radius:10px}
input,textarea,select{width:100%;padding:10px;margin:8px 0}
button{background:#0078ff;color:#fff;padding:10px;border:none;width:100%;cursor:pointer}
button:hover{background:#005fd1}
.msg{text-align:center;color:red;margin-top:10px}
</style>

<style>
body{font-family:Arial;background:#f4f6fa;margin:0}

/* NAVBAR */
.navbar{
    background:#0d6efd;
    padding:12px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.navbar .logo{
    color:#fff;
    font-size:20px;
    font-weight:bold;
    text-decoration:none;
}

.navbar ul{
    list-style:none;
    display:flex;
    gap:20px;
    margin:0;
    padding:0;
}

.navbar ul li a{
    color:#fff;
    text-decoration:none;
    font-size:15px;
}

.navbar ul li a:hover{
    text-decoration:underline;
}

/* FORM */
form{
    background:#fff;
    width:500px;
    margin:40px auto;
    padding:25px;
    border-radius:10px
}

input,textarea,select{
    width:100%;
    padding:10px;
    margin:8px 0
}

button{
    background:#0078ff;
    color:#fff;
    padding:10px;
    border:none;
    width:100%;
    cursor:pointer
}

button:hover{background:#005fd1}

.msg{text-align:center;color:red;margin-top:10px}
</style>

</head>

<body>
 
<div class="navbar">
    <a href="index.php" class="logo">Explore India</a>

    <ul>
        <li><a href="index.php">Home</a></li>

        <?php if ($isLoggedIn && $userType === 'business'): ?>
            <li><a href="add_listing.php">Add Listing</a></li>
        <?php endif; ?>

        <?php if ($isLoggedIn): ?>
            <li><a href="profile.php"><?= htmlspecialchars($userName) ?></a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</div>

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
    <option value="">Start Price Range</option>
    <option value="₹">₹ Budget</option>
    <option value="₹₹">₹₹ Medium</option>
    <option value="₹₹₹">₹₹₹ Premium</option>
</select>

<textarea name="services" placeholder="Services / Facilities"></textarea>

<input type="file" name="cover_image" accept="image/*">

<button type="submit">Submit Listing</button>

<div class="msg"><?php echo $message; ?></div>

</form>

</body>
</html>

<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access!");
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {

    $targetDir = "img/users/";

    // If folder missing then create
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES["profile_pic"]["name"]);
    $targetFile = $targetDir . $fileName;

    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ["jpg", "jpeg", "png", "webp"];

    if (!in_array($fileType, $allowedTypes)) {
        echo "Only JPG, JPEG, PNG & WEBP files allowed.";
        exit;
    }

    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile)) {
        
        // Update database
        $stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE id=?");
        $stmt->bind_param("si", $fileName, $user_id);
        $stmt->execute();

        echo "<script>alert('Profile picture updated!'); window.location='Profile-page.php';</script>";
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "No file selected.";
}
?>

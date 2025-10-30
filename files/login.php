<?php
session_start();
$conn = new mysqli("localhost", "root", "", "explore_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if user exists and verified
    $check = $conn->prepare("SELECT * FROM users WHERE email=? AND is_verified=1");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // ✅ Verify Password
        if (password_verify($password, $user['password'])) {

            // ✅ Store user session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_type'] = $user['user_type']; // NEW

            header("Location: index.php");
            exit;
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "Account not found or not verified!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Explore India</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        input {
            display: block;
            width: 260px;
            padding: 10px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #0078ff;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #005fd1;
        }
        .msg { color: red; margin-top: 10px; font-size: 14px; }
        h2 { text-align: center; }
    </style>
</head>
<body>

<form method="POST">
    <h2>Login to Explore India</h2>
    <input type="email" name="email" placeholder="Enter your email" required>
    <input type="password" name="password" placeholder="Enter your password" required>
    <button type="submit">Login</button>
    <div class="msg"><?php echo $message; ?></div>
</form>

</body>
</html>

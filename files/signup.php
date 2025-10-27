<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include 'db.php';

$message = '';
$otp_sent = false;

// Step 1: Send OTP
if (isset($_POST['send_otp'])) {
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($name) && !empty($password)) {
        $_SESSION['temp_user'] = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_time'] = time();

        // Send OTP Mail
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hello.exploring.india@gmail.com';
            $mail->Password = 'gumouriheohulqvq'; // Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('hello.exploring.india@gmail.com', 'Exploring India');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Exploring India';
            $mail->Body = "<h3>Hello $name,</h3>
                           <p>Your OTP for registration is <b>$otp</b>.</p>
                           <p>This code is valid for 5 minutes.</p>";

            $mail->send();
            $otp_sent = true;
            $message = "OTP sent to your email!";
        } catch (Exception $e) {
            $message = "Error sending OTP: {$mail->ErrorInfo}";
        }
    } else {
        $message = "Please fill all fields before sending OTP.";
    }
}

// Step 2: Verify OTP and Sign Up
if (isset($_POST['verify_signup'])) {
    $entered_otp = $_POST['otp'];

    if (isset($_SESSION['otp']) && time() - $_SESSION['otp_time'] <= 300) { // 5 minutes validity
        if ($entered_otp == $_SESSION['otp']) {
            $name = $_SESSION['temp_user']['name'];
            $email = $_SESSION['temp_user']['email'];
            $password = $_SESSION['temp_user']['password'];

            $check = $conn->prepare("SELECT * FROM users WHERE email=?");
            $check->bind_param("s", $email);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                $message = "Email already registered!";
            } else {
                $insert = $conn->prepare("INSERT INTO users (name, email, password, is_verified) VALUES (?, ?, ?, 1)");
                $insert->bind_param("sss", $name, $email, $password);
                $insert->execute();

                  // ✅ ye 3 lines add karo 👇
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_name'] = $name;

                // cleanup
                unset($_SESSION['otp']);
                unset($_SESSION['temp_user']);

                // ✅ redirect to home page
                header("Location: index.php");
                exit;
            }
        } else {
            $message = "Invalid OTP!";
        }
    } else {
        $message = "OTP expired. Please resend.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - Exploring India</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f6fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
            width: 320px;
        }
        h2 {
            text-align: center;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 12px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #0078ff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #005fd1;
        }
        p {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<form method="POST">
    <h2>Sign Up</h2>

    <input type="text" name="name" placeholder="Full Name" value="<?php echo $_SESSION['temp_user']['name'] ?? ''; ?>" required <?php echo $otp_sent ? 'readonly' : ''; ?>>
    <input type="email" name="email" placeholder="Email Address" value="<?php echo $_SESSION['temp_user']['email'] ?? ''; ?>" required <?php echo $otp_sent ? 'readonly' : ''; ?>>
    <input type="password" name="password" placeholder="Password" required <?php echo $otp_sent ? 'readonly' : ''; ?>>

    <?php if (!$otp_sent) { ?>
        <button type="submit" name="send_otp">Send OTP</button>
    <?php } else { ?>
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit" name="verify_signup">Verify & Sign Up</button>
    <?php } ?>

    <p><?php echo $message; ?></p>
</form>

</body>
</html>

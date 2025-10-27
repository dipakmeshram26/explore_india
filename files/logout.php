<?php
session_start();

// Sab session variables hatao
session_unset();

// Pura session destroy karo
session_destroy();

// Redirect back to home page
header("Location: index.php");
exit;
?>

<?php
session_name("user_session"); // Start session for user
session_start();

// Check if the user session is set
if (isset($_SESSION['email'])) {
    // If user is logged in, clear the user session data
    session_unset();
    session_destroy();
    header("Location: login.php"); // Redirect to login page
    exit();
}

// If no user session is set (i.e. not logged in), just redirect to login page
header("Location: login.php");
exit();
?>

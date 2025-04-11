<?php
session_name("admin_session"); // Set a custom session name for admin
session_start(); // Start the session for admin

// Check if the admin session is set
if (isset($_SESSION['admin_logged_in'])) {
    // If admin is logged in, clear the admin session data
    session_unset();
    session_destroy();
    header("Location: login.php"); // Redirect to login page
    exit();
}

// If no admin session is set (i.e. not logged in), just redirect to login page
header("Location: login.php");
exit();
?>

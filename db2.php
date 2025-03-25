<?php
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password is empty
$dbname = "shopping_cart"; // The database we created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$login_conn = new mysqli("localhost", "root", "", "shopping_cart"); // Replace with your actual database name

if ($login_conn->connect_error) {
    die("Connection failed: " . $login_conn->connect_error);
}
?>  
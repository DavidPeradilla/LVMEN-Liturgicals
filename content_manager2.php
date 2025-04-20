<?php
 session_name("admin_session");
 session_start();
 
 if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
     header("Location: login.php");
     exit();
 }
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Content Manager</title>
    <link rel="stylesheet" href="admin_styles.css">
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
/* General Styles */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

h2 {
    font-size: 30px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
}

a {
    text-decoration: none;
    color: inherit;
}

/* Main Content Styles */
.main-content {
    margin-left: 100px; /* Adjust for your sidebar width */
    padding: 240px;
    background-color: #ffffff;
    min-height: 100vh;
    margin-top: -223px;
}

.main-content h2 {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #2c3e50;
}

.main-content p {
    font-size: 18px;
    color: #7f8c8d;
    margin-bottom: 20px;
}

.main-content a {
    font-size: 18px;
    color: #3498db;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease;
}

.main-content a:hover {
    color: #2980b9;
}

/* Button Section */
.button-section {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.button-section a {
    display: block;
    padding: 15px 30px;
    background-color: #3498db;
    color: white;
    border-radius: 5px;
    text-align: center;
    font-size: 18px;
    width: 250px;
    transition: background-color 0.3s;
    margin: 10px 0;
}

.button-section a:hover {
    background-color: #2980b9;
    color: white;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .main-content {
        margin-left: 200px; /* Adjust for smaller sidebar */
    }

    .button-section a {
        width: 100%; /* Make buttons full width on smaller screens */
    }
}


    </style>

    
</head>
<body>

<div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="admin_sales.php"><i class="fas fa-tachometer-alt"></i><span>Overview</span></a>
        <a href="content_manager2.php"><i class="fas fa-cogs"></i><span>Content Manager</span></a>
        <a href="upload.php"><i class="fas fa-upload"></i><span>Manage Products</span></a>
        <a href="show_users2.php"><i class="fas fa-users"></i><span>Manage Users</span></a>
        <a href="admin_orders.php"><i class="fas fa-box"></i><span>Manage Orders</span></a>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i><span>Check Sales</span></a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>


<div class="main-content">
<h2 style=" text-align: center;">Content Manager</h2>

    <!-- Content Manager Actions -->
    <div class="button-section">
        <a href="admin_featured.php">Add Featured Products</a>
        <a href="slideshow.php">Add New Slideshow</a>
    </div>
</div>

</body>
</html>
<?php
session_start();
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

                /* Main Content */

                body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            text-align: center;
        }

        .main-content {
            margin-left: 450px;
            padding: 20px;
            width: 100%;
            min-height: 100vh;
        }

        .main-content h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .main-content p {
            font-size: 18px;
            margin-top: 10px;
        }

        .main-content a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .main-content a:hover {
            text-decoration: underline;
        }

        /* Buttons Section */
        .button-section {
            gap: 20px;
            margin-top: 20px;
           
        }

    </style>

    
</head>
<body>

<div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="admin_sales.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
        <a href="content_manager2.php"><i class="fas fa-cogs"></i><span>Content Manager</span></a>
        <a href="upload.php"><i class="fas fa-upload"></i><span>Manage Products</span></a>
        <a href="show_users2.php"><i class="fas fa-users"></i><span>Manage Users</span></a>
        <a href="admin_orders.php"><i class="fas fa-box"></i><span>Manage Orders</span></a>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i><span>Check Sales</span></a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>



<h2>Content Manager</h2>

  
    <!-- Content Manager Actions -->
    <div class="button-section">
        <a href="admin_featured.php">Add Featured Products</a>
    </div>

    <div class="button-section">
        <a href="slideshow.php">Add New Slideshow</a>
    </div>


</body>
</html>

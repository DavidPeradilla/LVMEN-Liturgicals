<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all slideshow images for management
$sql = "SELECT * FROM slideshow_images";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Slideshow</title>
    <link rel="stylesheet" href="admin_styles.css">
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            text-align: center;
        }
       
        table {
    width: 80%;  /* You can adjust the width as per your need */
    margin: 20px auto;  /* This centers the table */
    border-collapse: collapse;
}

th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

th {
    background-color: rgb(149, 152, 156);
    color: white;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}
    </style>

    
</head>
<body>

<div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
        <a href="content_manager.php"><i class="fas fa-cogs"></i><span>Content Manager</span></a>
        <a href="upload.php"><i class="fas fa-upload"></i><span>Manage Products</span></a>
        <a href="show_users2.php"><i class="fas fa-users"></i><span>Manage Users</span></a>
        <a href="admin_orders.php"><i class="fas fa-box"></i><span>Manage Orders</span></a>
        <a href="admin_sales.php"><i class="fas fa-chart-line"></i><span>Check Sales</span></a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

    <h1>Admin Dashboard - Slideshow Management</h1>

    <!-- Upload new slideshow image -->
    <h2>Upload New Slideshow Image</h2>
    <form action="upload_slideshow.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="slideshow_image" accept="image/*" required>
        <button type="submit" name="submit_image">Upload Image</button>
    </form>

    <!-- Manage Existing Slideshow Images -->
    <h3>Manage Slideshow Images</h3>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($image = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($image['image_path']); ?>" width="100"></td>
                    <td><?php echo $image['active'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <a href="delete_slideshow.php?id=<?php echo $image['id']; ?>" onclick="return confirm('Are you sure you want to delete this image?')">Delete</a> |
                        <a href="toggle_slideshow.php?id=<?php echo $image['id']; ?>"><?php echo $image['active'] ? 'Deactivate' : 'Activate'; ?></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

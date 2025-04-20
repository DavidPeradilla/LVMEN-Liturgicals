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

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM featured_products WHERE id = $id");
    header("Location: admin_featured.php");
    exit;
}

// Fetch all featured products
$result = $conn->query("SELECT * FROM featured_products");

// Get product count
$countResult = $conn->query("SELECT COUNT(*) as total FROM featured_products");
$countRow = $countResult->fetch_assoc();
$totalProducts = $countRow['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Featured Products</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            text-align: center;
        }

        h2 {
            margin: 20px 0;
            color: #333;
        }

        .container {
            width: 80%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
            padding: 20px;
            background: #eaeaea;
            border-radius: 10px;
        }

        input, button {
            width: 80%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: #28a745;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background: #333;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-delete {
            background-color: red;
            color: white;
        }

        .btn-complete {
            background-color: green;
            color: white;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .message {
            position: fixed;
            top: 20px;
            right: 500px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            font-weight: bold;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            opacity: 1;
            transition: opacity 1s ease-out;
        }

        .error {
            background-color: #f44336;
        }

        .success {
            background-color: #4CAF50;
        }

        .fade-out {
            opacity: 0;
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
        <a href="logout_admin.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<div class="container">
    <h2>Manage Featured Products</h2>

    <!-- Show messages -->
    <?php if (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] === "max_reached"): ?>
            <div class="message error">⚠️ You can only have 6 featured products. Remove one to add a new one.</div>
        <?php elseif ($_GET['error'] === "empty_fields"): ?>
            <div class="message error">⚠️ Please fill in all fields.</div>
        <?php elseif ($_GET['error'] === "image_error"): ?>
            <div class="message error">⚠️ There was an error uploading the image.</div>
        <?php endif; ?>
    <?php elseif (isset($_GET['success']) && $_GET['success'] === "added"): ?>
        <div class="message success">✅ Product added successfully!</div>
    <?php endif; ?>

    <form action="add_featured.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="text" name="description" placeholder="Description" required>
        <input type="file" name="image" required>
        <button type="submit" <?php if ($totalProducts >= 6) echo 'disabled'; ?>>Add Product</button>
    </form>

    <!-- Featured Products Table -->
    <table>
        <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><img src="<?= htmlspecialchars($row['image_path']); ?>" width="100"></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['description']); ?></td>
                <td>
                    <a href="edit_featured.php?id=<?= $row['id']; ?>" class="btn btn-complete">Edit</a>
                    <a href="?delete=<?= $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    window.onload = function () {
        const message = document.querySelector('.message');
        if (message) {
            // Fade out the message after 5 seconds
            setTimeout(() => {
                message.classList.add('fade-out');
                setTimeout(() => {
                    message.style.display = 'none';
                }, 1000);
            }, 5000);
        }
    };
</script>

</body>
</html>
<?php $conn->close(); ?>

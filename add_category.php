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


if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    
    $checkProducts = $conn->query("SELECT * FROM products WHERE category_id = '$delete_id'");
    if ($checkProducts->num_rows > 0) {
        die("<script>alert('Cannot delete category. It is assigned to products.'); window.location='add_category.php';</script>");
    }

    
    $conn->query("DELETE FROM categories WHERE id = '$delete_id'");
    header("Location: add_category.php?deleted=1");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category_name = $_POST['category_name'];

    
    $stmt = $conn->prepare("SELECT * FROM categories WHERE category_name = ?");
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {
        die("<script>alert('Category already exists.'); window.location='add_category.php';</script>");
    }

    
    $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
    $stmt->bind_param("s", $category_name);

    if ($stmt->execute()) {
        header("Location: add_category.php?success=1"); 
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}


$categories = $conn->query("SELECT * FROM categories");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Admin - Manage Categories</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }





        .container {
            width: 90%;
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h2 {
            color: #333;
        }

        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success, .error {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        td a {
            text-decoration: none;
            color: red;
            font-weight: bold;
        }

        td a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="admin_sales.php"><i class="fas fa-tachometer-alt"></i><span>Overview</span></a>
        <a href="upload.php"><i class="fas fa-upload"></i><span>Manage Products</span></a>
        <a href="admin_orders.php"><i class="fas fa-box"></i><span>Manage Orders</span></a>
        <a href="sales_analytics.php"><i class="fas fa-chart-line"></i><span>Check Sales</span></a>
        <a href="content_manager2.php"><i class="fas fa-cogs"></i><span>Content Manager</span></a>
        <a href="show_users2.php"><i class="fas fa-users"></i><span>Manage Users</span></a>
        <a href="logout_admin.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<div class="container">
    <h2>Manage Categories</h2>

    <?php if (isset($_GET['success'])) { echo "<p class='success'>✔ Category added successfully!</p>"; } ?>
    <?php if (isset($_GET['deleted'])) { echo "<p class='error'>✖ Category deleted successfully!</p>"; } ?>

    
    <form action="add_category.php" method="POST">
        <input type="text" name="category_name" placeholder="Category Name" required>
        <button type="submit">Add Category</button>
    </form>

    <h3>Existing Categories</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $categories->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['category_name']; ?></td>
                <td>
                    <a href="add_category.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this category?');">
                        Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <a href="upload.php" class="back-link">Back to Admin Dashboard</a>
</div>

</body>
</html>

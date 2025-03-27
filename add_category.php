<?php
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle category deletion
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Check if the category is being used in products
    $checkProducts = $conn->query("SELECT * FROM products WHERE category_id = '$delete_id'");
    if ($checkProducts->num_rows > 0) {
        die("<script>alert('Cannot delete category. It is assigned to products.'); window.location='add_category.php';</script>");
    }

    // Delete category
    $conn->query("DELETE FROM categories WHERE id = '$delete_id'");
    header("Location: add_category.php?deleted=1");
    exit();
}

// Handle adding new category
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category_name = $_POST['category_name'];

    // Check if category already exists
    $stmt = $conn->prepare("SELECT * FROM categories WHERE category_name = ?");
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {
        die("<script>alert('Category already exists.'); window.location='add_category.php';</script>");
    }

    // Insert new category
    $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
    $stmt->bind_param("s", $category_name);

    if ($stmt->execute()) {
        header("Location: add_category.php?success=1"); // Redirect after success
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}

// Fetch all categories for display
$categories = $conn->query("SELECT * FROM categories");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Categories</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            padding: 15px;
            color: white;
            width: 100%;
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar .nav-links {
            display: flex;
            gap: 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .logout {
            background-color: red;
            padding: 8px 12px;
            border-radius: 5px;
        }

        .logout:hover {
            background-color: darkred;
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
        <a href="dashboard.php">Dashboard</a>
        <a href="upload.php">Manage Products</a>
        <a href="show_users2.php">Manage Users</a>
        <a href="admin_orders.php">Manage Orders</a>
        <a href="admin_sales.php">Check Sales</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Manage Categories</h2>

    <?php if (isset($_GET['success'])) { echo "<p class='success'>✔ Category added successfully!</p>"; } ?>
    <?php if (isset($_GET['deleted'])) { echo "<p class='error'>✖ Category deleted successfully!</p>"; } ?>

    <!-- Add Category Form -->
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

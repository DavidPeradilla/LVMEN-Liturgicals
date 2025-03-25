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
        die("Cannot delete category. It is assigned to products.");
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
        die("Category already exists.");
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
</head>
<body>
    <h2>Admin - Manage Categories</h2>

    <?php if (isset($_GET['success'])) { echo "<p style='color: green;'>Category added successfully!</p>"; } ?>
    <?php if (isset($_GET['deleted'])) { echo "<p style='color: red;'>Category deleted successfully!</p>"; } ?>

    <!-- Add Category Form -->
    <form action="add_category.php" method="POST">
        <input type="text" name="category_name" placeholder="Category Name" required><br><br>
        <button type="submit">Add Category</button>
    </form>

    <h3>Existing Categories</h3>
    <table border="1">
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
                    <a href="add_category.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <p><a href="upload.php">Back to Admin Dashboard</a></p>
</body>
</html>

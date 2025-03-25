<?php
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories for dropdown
$categoryResult = $conn->query("SELECT * FROM categories");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['category'];

    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $imagePath = $target_file;
        } else {
            die("Error uploading file.");
        }
    } else {
        die("No file selected.");
    }

    $sql = "INSERT INTO products (name, price, quantity, image, category_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdisi", $name, $price, $quantity, $imagePath, $category_id);
    
    if ($stmt->execute()) {
        header("Location: upload.php?success=1");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
    
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Upload Product</title>
</head>
<body>
    <h2>Admin - Upload Product</h2>
    
    <?php if (isset($_GET['success'])) { echo "<p style='color: green;'>Product uploaded successfully!</p>"; } ?>
    
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required><br><br>
        <input type="number" name="price" placeholder="Price" required><br><br>
        <input type="number" name="quantity" placeholder="Quantity" required><br><br>

        <select name="category" required>
            <option value="">Select Category</option>
            <?php while ($category = $categoryResult->fetch_assoc()) { ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['category_name']; ?></option>
            <?php } ?>
        </select><br><br>

        <input type="file" name="image" accept="image/*" required><br><br>
        <button type="submit" onclick="this.disabled=true; this.form.submit();">Upload</button>
    </form>

    <p><a href="add_category.php">Add Category</a></p>
    <p><a href="view_products.php">View Products</a></p>
    <p><a href="dashboard.php">Back to Admin Dashboard</a></p>
    
</body>
</html>

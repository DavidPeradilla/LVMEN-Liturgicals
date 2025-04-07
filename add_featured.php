<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    
    // Validate inputs
    if (empty($name) || empty($price)) {
        header("Location: admin_featured.php?error=empty_fields");
        exit();
    }

    // Validate the number of featured products
    $countResult = $conn->query("SELECT COUNT(*) as total FROM featured_products");
    $countRow = $countResult->fetch_assoc();
    if ($countRow['total'] >= 6) {
        header("Location: admin_featured.php?error=max_reached");
        exit();
    }

    // Upload image
    if (isset($_FILES["image"])) {
        $imagePath = "uploads/" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO featured_products (name, image_path, price) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $imagePath, $price);
        $stmt->execute();

        header("Location: admin_featured.php?success=added");
    } else {
        header("Location: admin_featured.php?error=image_error");
    }
}
?>

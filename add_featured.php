<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    
    if (empty($name) || empty($description)) {
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

    // Handle image upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES["image"]["name"]);
        $imagePath = $targetDir . $imageName;
        $imageType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        // Check if file is an image and allowed type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageType, $allowedTypes)) {
            header("Location: admin_featured.php?error=image_error");
            exit();
        }

        // check file size (limit to 2MB)
        if ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
            header("Location: admin_featured.php?error=image_error");
            exit();
        }

        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            
            $stmt = $conn->prepare("INSERT INTO featured_products (name, image_path, description) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $imagePath, $description);
            $stmt->execute();

            header("Location: admin_featured.php?success=added");
            exit();
        } else {
            header("Location: admin_featured.php?error=image_error");
            exit();
        }
    } else {
        header("Location: admin_featured.php?error=image_error");
        exit();
    }
}
?>

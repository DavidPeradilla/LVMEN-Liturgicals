<?php
$conn = new mysqli("localhost", "root", "", "shopping_cart");

// Check database connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

if (isset($_POST['id'], $_POST['name'], $_POST['price'], $_POST['category_id'], $_POST['description'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $description = trim($_POST['description']);

    // Update product details
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, category_id = ?, description = ? WHERE id = ?");
    $stmt->bind_param("sdisi", $name, $price, $category_id, $description, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Error updating product: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Missing parameters"]);
}

$conn->close();
?>

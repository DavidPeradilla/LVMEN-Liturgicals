<?php
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $name = isset($_POST['name']) ? $conn->real_escape_string(trim($_POST['name'])) : null;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $description = isset($_POST['description']) ? $conn->real_escape_string(trim($_POST['description'])) : null;

    // Remove newlines from description
    $description = str_replace(array("\r", "\n"), '', $description); // Remove both \r (carriage return) and \n (line feed)

    // Debugging: Log the received POST data
    error_log(print_r($_POST, true)); // Log the POST data

    // Check if any value is missing
    if ($id === null || $name === null || $price === null || $quantity === null || $category_id === null || $description === null) {
        echo json_encode(["success" => false, "error" => "All fields are required."]);
        exit();
    }

    // Prepare the update query
    $sql = "UPDATE products SET name = ?, price = ?, quantity = ?, category_id = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["success" => false, "error" => "SQL preparation failed: " . $conn->error]);
        exit();
    }

    // Bind parameters and execute the query
    $stmt->bind_param("sdiiss", $name, $price, $quantity, $category_id, $description, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Update failed: " . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
}

$conn->close();
?>

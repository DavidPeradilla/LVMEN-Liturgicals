<?php
session_name("user_session");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "You must be logged in to add items to your cart.";
    exit;
}

$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and process the product_id and quantity
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;
    $email = $_SESSION['email'];

    if (!$product_id || $quantity <= 0) {
        echo "Invalid product or quantity";
        exit;
    }

    // Check if product exists
    $stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Product not found";
        exit;
    }

    $stmt->close();

    // Add to the cart logic
    $stmt = $conn->prepare("INSERT INTO cart (email, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
    $stmt->bind_param("siii", $email, $product_id, $quantity, $quantity);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

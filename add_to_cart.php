<?php
session_name("user_session"); // Only if you're using a custom session name consistently
session_start();

$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    echo "User not logged in";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate inputs
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;
    $email = $_SESSION['email'];

    if (!$product_id || $quantity <= 0) {
        echo "Invalid product or quantity";
        exit;
    }

    // Check if product exists (no need to check for stock)
    $stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Product not found";
        exit;
    }

    $stmt->close();

    // Check if product is already in the cart (using email instead of user_id)
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE email = ? AND product_id = ?");
    $stmt->bind_param("si", $email, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        // Update existing cart entry without stock validation
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE email = ? AND product_id = ?");
        $stmt->bind_param("isi", $quantity, $email, $product_id);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Update error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Insert new cart entry
        $stmt = $conn->prepare("INSERT INTO cart (email, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $email, $product_id, $quantity);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Insert error: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    echo "Invalid request";
}
    
$conn->close();
?>

<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    die("You need to log in to add items to the cart.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $email = $_SESSION['email'];

    // Check if product is already in the cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE email = ? AND product_id = ?");
    $stmt->bind_param("si", $email, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE email = ? AND product_id = ?");
        $stmt->bind_param("si", $email, $product_id);
        $stmt->execute();
        $_SESSION['message'] = "Quantity updated in cart!";
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO cart (email, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->bind_param("si", $email, $product_id);
        $stmt->execute();
        $_SESSION['message'] = "Product added to cart!";
    }

    header("Location: user_products.php");
    exit();
}

$conn->close();
?>

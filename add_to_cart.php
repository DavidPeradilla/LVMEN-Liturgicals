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
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Ensure quantity is valid
    if ($quantity < 1) {
        $quantity = 1;
    }

    $email = $_SESSION['email'];

    // Fetch product details
    $stmt = $conn->prepare("SELECT name, price, image FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close(); // ✅ Close the statement properly

    if (!$product) {
        die("Product not found.");
    }

    // Check if product is already in the cart
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE email = ? AND product_id = ?");
    $stmt->bind_param("si", $email, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartItem = $result->fetch_assoc();
    $stmt->close(); // ✅ Close the statement

    if ($cartItem) {
        // Update quantity if the product is already in the cart
        $newQuantity = $cartItem['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE email = ? AND product_id = ?");
        $stmt->bind_param("isi", $newQuantity, $email, $product_id);
    } else {
        // Insert new product into the cart
        $stmt = $conn->prepare("INSERT INTO cart (email, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $email, $product_id, $quantity);
    }

    // ✅ Execute the final query
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: view_cart.php");
        exit();
    } else {
        die("Error adding product: " . $stmt->error);
    }
}
?>

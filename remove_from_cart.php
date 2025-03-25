<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email'])) {
    die("You need to log in first.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cart_id = $_POST['cart_id'];
    $email = $_SESSION['email'];

    // Delete item from cart where it belongs to the logged-in user
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND email = ?");
    $stmt->bind_param("is", $cart_id, $email);

    if ($stmt->execute()) {
        header("Location: view_cart.php"); // Redirect to cart after removing item
        exit();
    } else {
        echo "Error removing item.";
    }
}

$conn->close();
?>

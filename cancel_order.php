<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if order_id is received from form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Update order status to 'Canceled'
    $stmt = $conn->prepare("UPDATE orders SET order_status = 'Canceled' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        // Redirect back to profile page after successful cancellation
        header("Location: profile.php");
        exit();
    } else {
        echo "Error updating order status.";
    }

    $stmt->close();
}

$conn->close();
?>

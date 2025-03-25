<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure this is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    // Validate order status value
    $valid_statuses = ["Pending", "Processing", "Shipped", "Delivered"];
    if (!in_array($order_status, $valid_statuses)) {
        die("Invalid status.");
    }

    // Update order status in the database
    $sql = "UPDATE orders SET order_status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $order_status, $order_id);

    if ($stmt->execute()) {
        header("Location: admin_orders.php"); // Redirect back to admin orders page
        exit();
    } else {
        echo "Error updating order: " . $conn->error;
    }

    $stmt->close();
}
$conn->close();
?>

<?php
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Capture the order ID and cancellation reason from the form
$order_id = $_POST['order_id'];
$cancellation_reason = $_POST['reason'];  // Assuming 'reason' is the name of the textarea input field

// Prepare the update query to update order status and store cancellation reason
$cancel_query = "UPDATE orders SET order_status = 'Canceled', cancellation_reason = ? WHERE id = ?";
$stmt = $conn->prepare($cancel_query);
$stmt->bind_param("si", $cancellation_reason, $order_id);  // 'si' = string, integer

// Execute the query
if ($stmt->execute()) {
    echo "Order canceled successfully.";
} else {
    echo "Error canceling order.";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

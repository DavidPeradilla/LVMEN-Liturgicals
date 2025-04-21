<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['reason'])) {
    $order_id = $_POST['order_id'];
    $reason = $_POST['reason'];

    // Update order status to canceled and save the reason
    $stmt = $conn->prepare("UPDATE orders SET order_status = 'Canceled', cancellation_reason = ? WHERE id = ?");
    $stmt->bind_param("si", $reason, $order_id);
    if ($stmt->execute()) {
        // Set session variable to show success message
        $_SESSION['cancel_message'] = "Order #$order_id has been successfully canceled.";
    } else {
        // Set error message if cancellation fails
        $_SESSION['cancel_message'] = "There was an error canceling your order. Please try again.";
    }
    $stmt->close();
}

$conn->close();

// Redirect back to the profile page
header("Location: profile.php");
exit();
?>

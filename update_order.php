<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debugging: Log POST data
error_log(print_r($_POST, true));

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']); // Ensure integer

    if (isset($_POST['order_status'])) {
        // Updating status from dropdown
        $order_status = $_POST['order_status'];
        $valid_statuses = ["Pending", "Processing", "Shipped", "Delivered"];

        if (!in_array($order_status, $valid_statuses)) {
            $_SESSION['message'] = "Invalid order status.";
            header("Location: admin_orders.php");
            exit();
        }

        $sql = "UPDATE orders SET order_status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $order_status, $order_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Order #$order_id updated to '$order_status'.";
        } else {
            $_SESSION['message'] = "Error updating order: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['mark_delivered'])) {
        // Marking order as Delivered
        $sql = "UPDATE orders SET order_status = 'Delivered' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Order #$order_id marked as Delivered.";
        } else {
            $_SESSION['message'] = "Error marking order as Delivered: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}

// Redirect back to admin_orders.php
header("Location: admin_orders.php");
exit();
?>
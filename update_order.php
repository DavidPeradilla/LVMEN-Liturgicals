<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    if (isset($_POST['mark_delivered'])) {
        // ✅ Mark as Delivered
        $sql = "UPDATE orders SET order_status = 'Delivered', status_updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Order #$order_id marked as Delivered.";
        } else {
            $_SESSION['message'] = "Error marking order as Delivered: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['order_status'])) {
        // ✅ Dropdown status update
        $order_status = $_POST['order_status'];
        $valid_statuses = ["Pending", "Processing", "Shipped", "Delivered", "Canceled"];

        if (!in_array($order_status, $valid_statuses)) {
            $_SESSION['message'] = "Invalid order status.";
            header("Location: admin_orders.php");
            exit();
        }

        if ($order_status == "Canceled") {
            $cancellation_reason = isset($_POST['cancellation_reason']) ? trim($_POST['cancellation_reason']) : "";
            $sql = "UPDATE orders SET order_status = ?, cancellation_reason = ?, status_updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $order_status, $cancellation_reason, $order_id);
        } else {
            // Clear any old reason if not canceled
            $sql = "UPDATE orders SET order_status = ?, cancellation_reason = NULL, status_updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $order_status, $order_id);
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = "Order #$order_id updated to '$order_status'.";
        } else {
            $_SESSION['message'] = "Error updating order: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
header("Location: admin_orders.php");
exit();

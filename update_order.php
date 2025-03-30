<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debugging: Log POST data
error_log(print_r($_POST, true));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['order_id'])) {
        $order_id = intval($_POST['order_id']); // Ensure integer

        if (isset($_POST['order_status'])) {
            // Updating status from dropdown
            $order_status = $_POST['order_status'];
            $valid_statuses = ["Pending", "Processing", "Shipped", "Delivered"];

            if (!in_array($order_status, $valid_statuses)) {
                die("Invalid status.");
            }

            $sql = "UPDATE orders SET order_status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $order_status, $order_id);
        
        } elseif (isset($_POST['mark_completed'])) {
            // Updating status to "Completed" from button
            $sql = "UPDATE orders SET order_status = 'Completed' WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $order_id);
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = "Order #$order_id updated successfully.";
        } else {
            $_SESSION['message'] = "Error updating order: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = "Order ID is missing.";
    }
}

$conn->close();
header("Location: admin_orders.php");
exit();
?>

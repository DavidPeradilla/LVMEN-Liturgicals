<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Soft-remove the order by setting is_removed to 1
    $sql = "UPDATE orders SET is_removed = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Order removed from the list successfully.";
    } else {
        $_SESSION['message'] = "Error removing order from the list.";
    }

    $stmt->close();
}

$conn->close();
header("Location: admin_orders.php");
exit();
?>


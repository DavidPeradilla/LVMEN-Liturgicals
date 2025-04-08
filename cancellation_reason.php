<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $stmt = $conn->prepare("SELECT cancellation_reason FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->bind_result($cancellation_reason);
    $stmt->fetch();
    $stmt->close();
    
    echo htmlspecialchars($cancellation_reason) ?: "No cancellation reason provided.";
}

$conn->close();
?>

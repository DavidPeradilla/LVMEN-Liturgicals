<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database connection failed."]));
}

if (!isset($_SESSION['email'])) {
    die(json_encode(["success" => false, "error" => "User not logged in."]));
}

if (isset($_POST['cart_id'], $_POST['quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) {
        echo json_encode(["success" => false, "error" => "Invalid quantity."]);
        exit();
    }

    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $cart_id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Update failed."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid request."]);
}

$conn->close();
?>

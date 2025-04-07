<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve values from the form
    $courier_name = $_POST['courier_name'];
    $tracking_link = $_POST['tracking_link'];
    $order_id = $_SESSION['order']['id']; // Assuming the order ID is stored in the session

    // Update the order with the courier info
    $stmt = $conn->prepare("UPDATE orders SET courier_name = ?, tracking_link = ? WHERE id = ?");
    $stmt->bind_param("ssi", $courier_name, $tracking_link, $order_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Courier info updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update courier info!";
    }

    // Redirect back to the order tracking page (or any page you want)
    header("Location: order_details.php");  // Assuming this is the tracking page
    exit;
}

$conn->close();
?>

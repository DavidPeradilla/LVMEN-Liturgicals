<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['order_id'])) {
    die("No order ID provided.");
}

$order_id = $_GET['order_id'];

// Fetch order details
$stmt = $conn->prepare("SELECT id, recipient_name, email, total_price, order_status FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Order not found.");
}

$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Tracking</title>
    <link rel="stylesheet" type="text/css" href="LVMEN.css">
</head>
<body>

<h2>Order Tracking</h2>

<table border="1">
    <tr>
        <th>Order ID</th>
        <td><?php echo $order['id']; ?></td>
    </tr>
    <tr>
        <th>Recipient Name</th>
        <td><?php echo htmlspecialchars($order['recipient_name']); ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?php echo htmlspecialchars($order['email']); ?></td>
    </tr>
    <tr>
        <th>Total Price</th>
        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
    </tr>
    <tr>
        <th>Status</th>
        <td><strong><?php echo htmlspecialchars($order['order_status']); ?></strong></td>
    </tr>
</table>

<p><a href="dashboard.php">Back to Dashboard</a></p>

</body>
</html>

<?php $conn->close(); ?>

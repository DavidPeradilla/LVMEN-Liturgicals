<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

$sql = "SELECT orders.id, orders.recipient_name, orders.phone_number, orders.total_price,
               orders.gcash_number, orders.gcash_reference, orders.payment_screenshot,
               order_items_backup.product_name, order_items_backup.quantity
        FROM orders
        LEFT JOIN order_items_backup ON orders.id = order_items_backup.order_id
        WHERE orders.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 20px; }
        h2 { text-align: center; }
        .container { width: 70%; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        img { width: 50px; border-radius: 5px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Order Details (Order ID: <?php echo $order_id; ?>)</h2>
    
    <p><strong>Recipient:</strong> <?php echo htmlspecialchars($order['recipient_name']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone_number']); ?></p>
    <p><strong>Total Price:</strong> ₱<?php echo number_format($order['total_price'], 2); ?></p>
    <p><strong>GCash Number:</strong> <?php echo htmlspecialchars($order['gcash_number']); ?></p>
    <p><strong>GCash Reference:</strong> <?php echo htmlspecialchars($order['gcash_reference']); ?></p>
    
    <p><strong>Payment Screenshot:</strong></p>
    <?php if (!empty($order['payment_screenshot'])) { ?>
        <a href="payment_screenshots/<?php echo htmlspecialchars($order['payment_screenshot']); ?>" target="_blank">
            <img src="payment_screenshots/<?php echo htmlspecialchars($order['payment_screenshot']); ?>" alt="Screenshot">
        </a>
    <?php } else { echo "No Screenshot"; } ?>

    <h3>Ordered Products</h3>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
        </tr>
        <?php
        do { ?>
            <tr>
                <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
            </tr>
        <?php } while ($order = $result->fetch_assoc()); ?>
    </table>
    
    <br>
    <a href="admin_orders.php"><button class="btn btn-back">Back to Orders</button></a>
</div>
</body>
</html>

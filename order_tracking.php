<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$order = null;
$error_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];

    // Fetch order details and order items
    $stmt = $conn->prepare("
    SELECT o.id, o.recipient_name, o.email, o.total_price, o.order_status, 
           oi.product_id, oi.quantity, oi.price, p.name
    FROM orders o
    LEFT JOIN order_items_backup oi ON o.id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE o.id = ?
");

    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Collect order details and products
        $order_items = [];
        while ($row = $result->fetch_assoc()) {
            $order_items[] = $row;
        }
        $order = $order_items[0];  // Fetch the first row as general order details
    } else {
        $error_message = "Order not found. Please check your Order ID.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Track Your Order</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background: #007aff;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        .status {
            font-weight: bold;
            color: #007aff;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007aff;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Track Your Order</h2>

    <?php if ($order): ?>
        <h3>Order Details</h3>
        <table>
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
                <td class="status"><?php echo htmlspecialchars($order['order_status']); ?></td>
            </tr>
        </table>

        <h3>Products Ordered</h3>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
            <?php foreach ($order_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($error_message): ?>
        <p style="color: red;"> <?php echo $error_message; ?> </p>
    <?php endif; ?>

    <a href="profile.php" class="back-link">Back to Profile</a>
</div>

</body>
</html>

<?php $conn->close(); ?>

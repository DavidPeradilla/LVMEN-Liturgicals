<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch orders with ordered products
$sql = "SELECT id, email, recipient_name, phone_number, total_price, order_status, gcash_number, gcash_reference, payment_screenshot
        FROM orders 
        ORDER BY id DESC";
$orders_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 20px; }
        h2 { text-align: center; }
        .container { width: 90%; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        .btn { padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .btn-delete { background-color: red; color: white; }
        .btn-delete:hover { opacity: 0.8; }
        select { padding: 5px; border-radius: 5px; }
        img { width: 50px; height: auto; border-radius: 5px; }
        .btn-view { background-color: #28a745; color: white; }
        .btn-view:hover { opacity: 0.8; }
    </style>
</head>
<body>
<div class="container">
    <h2>Manage Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Email</th> <!-- New Email Column -->
            <th>Recipient Name</th>
            <th>Phone Number</th>
            <th>Total Price</th>
            <th>GCash Number</th>
            <th>GCash Reference</th>
            <th>Payment Screenshot</th>
            <th>Ordered Products</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($order = $orders_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo htmlspecialchars($order['email']); ?></td> <!-- Display Email -->
                <td><?php echo htmlspecialchars($order['recipient_name']); ?></td>
                <td><?php echo htmlspecialchars($order['phone_number']); ?></td>
                <td>₱<?php echo number_format($order['total_price'], 2); ?></td>
                <td><?php echo htmlspecialchars($order['gcash_number']); ?></td>
                <td><?php echo htmlspecialchars($order['gcash_reference']); ?></td>
                <td>
                    <?php if (!empty($order['payment_screenshot'])) { ?>
                        <a href="payment_screenshots/<?php echo htmlspecialchars($order['payment_screenshot']); ?>" target="_blank">
                        <img src="<?php echo htmlspecialchars($order['payment_screenshot']); ?>" width="200" alt="Payment Screenshot">
                        </a>
                    <?php } else { echo "No Screenshot"; } ?>
                </td>
                <td>
                    <a href="order_details.php?order_id=<?php echo $order['id']; ?>">
                        <button class="btn btn-view">View Items</button>
                    </a>
                </td>
                <td>
                    <form action="update_order.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="order_status" onchange="this.form.submit()">
                            <option value="Pending" <?php if ($order['order_status'] == "Pending") echo "selected"; ?>>Pending</option>
                            <option value="Processing" <?php if ($order['order_status'] == "Processing") echo "selected"; ?>>Processing</option>
                            <option value="Shipped" <?php if ($order['order_status'] == "Shipped") echo "selected"; ?>>Shipped</option>
                            <option value="Delivered" <?php if ($order['order_status'] == "Delivered") echo "selected"; ?>>Delivered</option>
                        </select>
                    </form>
                </td>
                <td>
                    <form action="delete_order.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" class="btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>  
    </table>
</div>
</body>
</html>

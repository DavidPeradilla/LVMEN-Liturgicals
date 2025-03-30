<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total sales revenue
$total_sales_query = "SELECT SUM(total_price) AS total_revenue FROM orders WHERE order_status = 'Completed'";
$total_sales_result = $conn->query($total_sales_query);
$total_sales = $total_sales_result->fetch_assoc()['total_revenue'] ?? 0;

// Fetch total product sales
$total_products_query = "SELECT SUM(quantity) AS total_products_sold 
                         FROM order_items_backup 
                         JOIN orders ON order_items_backup.order_id = orders.id
                         WHERE orders.order_status = 'Completed'";
$total_products_result = $conn->query($total_products_query);
$total_products_sold = $total_products_result->fetch_assoc()['total_products_sold'] ?? 0;

// Fetch completed orders
$completed_orders_query = "SELECT * FROM orders WHERE order_status = 'Completed' ORDER BY id DESC";
$completed_orders_result = $conn->query($completed_orders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Statistics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin-top: 20px;
        }
        h2, h3 {
            text-align: center;
            color: #333;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .stat-box {
            background: #007bff;
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-size: 18px;
            width: 45%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            display: block;
            width: 200px;
            padding: 10px;
            text-align: center;
            background: #28a745;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin: 20px auto;
            font-size: 16px;
        }
        .btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Sales Statistics</h2>
        <div class="stats">
            <div class="stat-box">
                <i class="fas fa-money-bill-wave"></i> <br>
                <strong>Total Sales Revenue:</strong> ₱<?php echo number_format($total_sales, 2); ?>
            </div>
            <div class="stat-box">
                <i class="fas fa-box"></i> <br>
                <strong>Total Products Sold:</strong> <?php echo number_format($total_products_sold); ?>
            </div>
        </div>
        <h3>Completed Orders</h3>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Email</th>
                <th>Recipient Name</th>
                <th>Phone Number</th>
                <th>Total Price</th>
                <th>Order Date</th>
            </tr>
            <?php while ($order = $completed_orders_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                    <td><?php echo htmlspecialchars($order['recipient_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['phone_number']); ?></td>
                    <td>₱<?php echo number_format($order['total_price'], 2); ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                </tr>
            <?php } ?>
        </table>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>

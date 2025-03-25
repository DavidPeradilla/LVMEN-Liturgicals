<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Total sales revenue
$total_sales_query = "SELECT SUM(total_price) AS total_revenue FROM orders WHERE order_status = 'Completed'";
$total_sales_result = $conn->query($total_sales_query);
$total_sales = $total_sales_result->fetch_assoc()['total_revenue'] ?? 0;

// Total number of orders
$total_orders_query = "SELECT COUNT(id) AS total_orders FROM orders WHERE order_status = 'Completed'";
$total_orders_result = $conn->query($total_orders_query);
$total_orders = $total_orders_result->fetch_assoc()['total_orders'] ?? 0;

// Total products sold
$total_products_query = "SELECT SUM(quantity) AS total_products FROM order_items_backup
                        JOIN orders ON order_items_backup.order_id = orders.id
                        WHERE orders.order_status = 'Completed'";
$total_products_result = $conn->query($total_products_query);
$total_products = $total_products_result->fetch_assoc()['total_products'] ?? 0;

// Sales per month
$sales_per_month_query = "SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, SUM(total_price) AS revenue
                          FROM orders WHERE order_status = 'Completed'
                          GROUP BY month ORDER BY month DESC";
$sales_per_month_result = $conn->query($sales_per_month_query);
$sales_data = [];
while ($row = $sales_per_month_result->fetch_assoc()) {
    $sales_data[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Statistics</title>
    <link rel="stylesheet" href="LVMEN.css">
</head>
<body>
    <h2>Sales Statistics</h2>
    <p><strong>Total Sales Revenue:</strong> $<?php echo number_format($total_sales, 2); ?></p>
    <p><strong>Total Orders:</strong> <?php echo $total_orders; ?></p>
    <p><strong>Total Products Sold:</strong> <?php echo $total_products; ?></p>
    
    <h3>Sales Per Month</h3>
    <table border="1">
        <tr>
            <th>Month</th>
            <th>Revenue</th>
        </tr>
        <?php foreach ($sales_data as $row) { ?>
            <tr>
                <td><?php echo $row['month']; ?></td>
                <td>$<?php echo number_format($row['revenue'], 2); ?></td>
            </tr>
        <?php } ?>
    </table>
    
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

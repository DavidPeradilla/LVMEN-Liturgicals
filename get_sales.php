<?php
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total sales revenue
$sales_query = "SELECT SUM(total_price) AS total_revenue FROM orders WHERE order_status = 'Completed'";
$sales_result = $conn->query($sales_query);
$total_sales = $sales_result->fetch_assoc()['total_revenue'] ?? 0;

echo $total_sales; // Output the total sales revenue
?>

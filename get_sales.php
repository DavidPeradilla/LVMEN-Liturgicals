<?php
header("Content-Type: application/json"); // Ensure JSON response
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get total sales revenue from "Delivered" orders
$sales_query = "SELECT SUM(total_price) AS total_revenue FROM orders WHERE order_status = 'Delivered'";
$sales_result = $conn->query($sales_query);

$total_sales = $sales_result->fetch_assoc()['total_revenue'] ?? 0;

echo json_encode(["total_sales" => $total_sales]); // Return JSON response
?>

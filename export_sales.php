<?php
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$month = $_POST['month'] ?? date('m');
$year = $_POST['year'] ?? date('Y');

// Fetch data

// Monthly sales (Delivered + Removed)
$monthly_sales_query = "SELECT SUM(total_price) AS total_revenue 
                        FROM orders 
                        WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                        AND MONTH(order_date) = '$month' 
                        AND YEAR(order_date) = '$year'";
$monthly_sales = $conn->query($monthly_sales_query)->fetch_assoc()['total_revenue'] ?? 0;

// Monthly canceled sales
$monthly_canceled_query = "SELECT SUM(total_price) AS canceled_revenue 
                           FROM orders 
                           WHERE order_status = 'Canceled'
                           AND MONTH(order_date) = '$month' 
                           AND YEAR(order_date) = '$year'";
$monthly_canceled = $conn->query($monthly_canceled_query)->fetch_assoc()['canceled_revenue'] ?? 0;

// Overall total sales
$total_sales_query = "SELECT SUM(total_price) AS total_revenue 
                      FROM orders 
                      WHERE order_status = 'Delivered' OR order_status = 'Removed'";
$total_sales = $conn->query($total_sales_query)->fetch_assoc()['total_revenue'] ?? 0;

// Yearly sales
$yearly_sales_query = "SELECT SUM(total_price) AS total_revenue 
                       FROM orders 
                       WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                       AND YEAR(order_date) = '$year'";
$yearly_sales = $conn->query($yearly_sales_query)->fetch_assoc()['total_revenue'] ?? 0;

// Total canceled revenue
$canceled_sales_query = "SELECT SUM(total_price) AS canceled_revenue 
                         FROM orders 
                         WHERE order_status = 'Canceled'";
$total_canceled = $conn->query($canceled_sales_query)->fetch_assoc()['canceled_revenue'] ?? 0;

// Total products sold (all-time)
$total_products_query = "SELECT SUM(order_items_backup.quantity) AS total_products_sold 
                         FROM order_items_backup 
                         JOIN orders ON order_items_backup.order_id = orders.id
                         WHERE orders.order_status = 'Delivered' OR orders.order_status = 'Removed'";
$total_products_sold = $conn->query($total_products_query)->fetch_assoc()['total_products_sold'] ?? 0;

// Prepare CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sales_summary_'.$month.'_'.$year.'.csv"');

$output = fopen('php://output', 'w');

// CSV Headings
fputcsv($output, ['Sales Data']);
fputcsv($output, ['Month', date("F", mktime(0, 0, 0, $month, 1))]);
fputcsv($output, ['Year', $year]);
fputcsv($output, []); // empty line

// Sales Breakdown
fputcsv($output, ['Monthly Sales', number_format($monthly_sales, 2)]);
fputcsv($output, ['Monthly Canceled Sales', number_format($monthly_canceled, 2)]);
fputcsv($output, ['Yearly Sales', number_format($yearly_sales, 2)]);
fputcsv($output, ['Total Sales (All Time)', number_format($total_sales, 2)]);
fputcsv($output, ['Total Products Sold (All Time)', number_format($total_products_sold)]);
fputcsv($output, ['Total Canceled Orders (All Time)', number_format($total_canceled, 2)]);

fclose($output);
exit;
?>

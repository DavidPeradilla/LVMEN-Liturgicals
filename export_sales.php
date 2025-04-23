<?php
// Include FPDF library
require('libs/fpdf.php');

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

// Check if user requested to export PDF
if (isset($_GET['export_pdf'])) {
    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Title
    $pdf->Cell(200, 10, 'Sales Summary for ' . date("F", mktime(0, 0, 0, $month, 1)) . ' ' . $year, 0, 1, 'C');
    $pdf->Ln(10); // Line break

    // Table Header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(100, 10, 'Sales Data', 1, 0, 'C');
    $pdf->Cell(90, 10, 'Amount', 1, 1, 'C');
    
    // Table Content
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(100, 10, 'Monthly Sales', 1);
    $pdf->Cell(90, 10, number_format($monthly_sales, 2), 1, 1, 'R');
    
    $pdf->Cell(100, 10, 'Monthly Canceled Sales', 1);
    $pdf->Cell(90, 10, number_format($monthly_canceled, 2), 1, 1, 'R');
    
    $pdf->Cell(100, 10, 'Yearly Sales', 1);
    $pdf->Cell(90, 10, number_format($yearly_sales, 2), 1, 1, 'R');
    
    $pdf->Cell(100, 10, 'Total Sales (All Time)', 1);
    $pdf->Cell(90, 10, number_format($total_sales, 2), 1, 1, 'R');
    
    $pdf->Cell(100, 10, 'Total Products Sold (All Time)', 1);
    $pdf->Cell(90, 10, number_format($total_products_sold), 1, 1, 'R');
    
    $pdf->Cell(100, 10, 'Total Canceled Orders (All Time)', 1);
    $pdf->Cell(90, 10, number_format($total_canceled, 2), 1, 1, 'R');

    // Output the PDF
    $pdf->Output('D', 'Sales_Report_' . date("F", mktime(0, 0, 0, $month, 1)) . '_' . $year . '.pdf');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - <?php echo date("F", mktime(0, 0, 0, $month, 1)) . " " . $year; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>

@media print {
        .btn-container {
            display: none;
        }
    }
    </style>



</head>


<body class="bg-gray-100 text-gray-900">

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Sales Summary for <?php echo date("F", mktime(0, 0, 0, $month, 1)) . " " . $year; ?></h1>

     <div class="btn-container"> 
        <div class="flex justify-center mb-6">
            <button class="bg-green-500 text-white py-2 px-6 rounded-lg hover:bg-green-600 transition duration-300" onclick="window.print();">Print Report</button>
        </div>
    </div>
    
    <div class="btn-container">
        <div class="flex justify-center mb-6">
            <a href="?export_pdf=1" class="bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600 transition duration-300">Export to PDF</a>
        </div>
    </div>
      
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full table-auto text-left">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-6 text-sm font-semibold text-gray-700">Sales Data</th>
                        <th class="py-3 px-6 text-sm font-semibold text-gray-700">Amount</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <tr class="border-t">
                        <td class="py-3 px-6">Monthly Sales</td>
                        <td class="py-3 px-6"><?php echo number_format($monthly_sales, 2); ?></td>
                    </tr>
                    <tr class="border-t">
                        <td class="py-3 px-6">Monthly Canceled Sales</td>
                        <td class="py-3 px-6"><?php echo number_format($monthly_canceled, 2); ?></td>
                    </tr>
                    <tr class="border-t">
                        <td class="py-3 px-6">Yearly Sales</td>
                        <td class="py-3 px-6"><?php echo number_format($yearly_sales, 2); ?></td>
                    </tr>
                    <tr class="border-t">
                        <td class="py-3 px-6">Total Sales (All Time)</td>
                        <td class="py-3 px-6"><?php echo number_format($total_sales, 2); ?></td>
                    </tr>
                    <tr class="border-t">
                        <td class="py-3 px-6">Total Products Sold (All Time)</td>
                        <td class="py-3 px-6"><?php echo number_format($total_products_sold); ?></td>
                    </tr>
                    <tr class="border-t">
                        <td class="py-3 px-6">Total Canceled Orders (All Time)</td>
                        <td class="py-3 px-6"><?php echo number_format($total_canceled, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>




</body>
</html>

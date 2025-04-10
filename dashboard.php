<?php
include 'db2.php'; // Ensure this file sets $conn
$conn = new mysqli("localhost", "root", "", "shopping_cart");


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$total_sales_query = "SELECT SUM(total_price) AS total_revenue FROM orders WHERE order_status = 'Delivered'";
$total_sales_result = $conn->query($total_sales_query);
$total_sales = $total_sales_result->fetch_assoc()['total_revenue'] ?? 0;


$selected_year = $_GET['year'] ?? date('Y');
$yearly_sales_query = "SELECT SUM(total_price) AS total_revenue 
                        FROM orders 
                        WHERE order_status = 'Delivered' 
                        AND YEAR(order_date) = '$selected_year'";
$yearly_sales_result = $conn->query($yearly_sales_query);
$yearly_sales = $yearly_sales_result->fetch_assoc()['total_revenue'] ?? 0;


$monthly_sales_query = "SELECT MONTH(order_date) AS month, SUM(total_price) AS total_revenue
                        FROM orders
                        WHERE order_status = 'Delivered' 
                        AND YEAR(order_date) = '$selected_year'
                        GROUP BY MONTH(order_date)";
$monthly_sales_result = $conn->query($monthly_sales_query);

$monthly_sales_data = array_fill(0, 12, 0); 
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

while ($row = $monthly_sales_result->fetch_assoc()) {
    $monthly_sales_data[$row['month'] - 1] = $row['total_revenue']; 
}


$canceled_sales_query = "SELECT MONTH(order_date) AS month, SUM(total_price) AS total_revenue 
FROM orders 
WHERE order_status = 'Canceled' 
AND YEAR(order_date) = '$selected_year'
GROUP BY MONTH(order_date)";

$canceled_sales_result = $conn->query($canceled_sales_query);

$canceled_sales_data = array_fill(0, 12, 0); 
while ($row = $canceled_sales_result->fetch_assoc()) {
    $canceled_sales_data[$row['month'] - 1] = $row['total_revenue']; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sidebar2.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>Sales Statistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 0px 10px gray;
            border-radius: 5px;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .stat-box {
            padding: 20px;
            background: rgb(92, 95, 93);
            color: white;
            border-radius: 5px;
            width: 30%;
        }

        .content {
            margin-left: 260px;
            padding: 20px;
            flex-grow: 1;
        }
        .content h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .chart-container {
            width: 100%;
            max-width: 600px;
            margin: auto;
        }

        form {
            margin: 20px;
        }
        select, button {
            padding: 10px;
            font-size: 16px;
            margin: 5px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="admin_sales.php"><i class="fas fa-tachometer-alt"></i><span>Overview</span></a>
        <a href="content_manager2.php"><i class="fas fa-cogs"></i><span>Content Manager</span></a>
        <a href="upload.php"><i class="fas fa-upload"></i><span>Manage Products</span></a>
        <a href="show_users2.php"><i class="fas fa-users"></i><span>Manage Users</span></a>
        <a href="admin_orders.php"><i class="fas fa-box"></i><span>Manage Orders</span></a>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i><span>Check Sales</span></a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>
<div class="container">
    <h2>Sales Statistics</h2>

    <!-- Filter Form -->
    <form method="GET" action="">
        <label for="year">Select Year:</label>
        <select name="year" id="year">
            <?php
            $current_year = date('Y');
            for ($i = 2020; $i <= $current_year; $i++) {
                echo '<option value="' . $i . '" ' . ($selected_year == $i ? 'selected' : '') . '>' . $i . '</option>';
            }
            ?>
        </select>
        <button type="submit">Filter</button>
    </form>

    <!-- Chart Container -->
    <div class="chart-container">
        <canvas id="salesChart" width="400" height="200"></canvas>
    </div>

    <script>
        // Fetch data from PHP variables
        var monthlySales = <?php echo json_encode($monthly_sales_data); ?>;
        var canceledSales = <?php echo json_encode($canceled_sales_data); ?>;
        var months = <?php echo json_encode($months); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            
            var ctx = document.getElementById('salesChart').getContext('2d');
            var salesChart = new Chart(ctx, {
                type: 'bar', 
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Monthly Sales Revenue',
                        data: monthlySales,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        type: 'bar' /
                    },
                    {
                        label: 'Canceled Orders Revenue',
                        data: canceledSales,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        fill: false, 
                        type: 'line', 
                        tension: 0.4 
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</div>

</body>
</html>

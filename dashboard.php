<?php
include 'db2.php'; // Ensure this file sets $conn
$conn = new mysqli("localhost", "root", "", "shopping_cart");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT COUNT(id) AS total_users FROM users";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_users = $row['total_users'];

// Fetch latest users
$sql_latest = "SELECT id, first_name, last_name,email, password FROM users ORDER BY id DESC LIMIT 5";
$result_latest = $conn->query($sql_latest);


// Fetch all users
$sql = "SELECT id, first_name, last_name, email FROM users";
$result = $conn->query($sql);

// Get selected month and year from form input (default to current month and year)
$selected_month = $_GET['month'] ?? date('m');
$selected_year = $_GET['year'] ?? date('Y');

// Fetch Overall Total Sales (All Time)
$total_sales_query = "SELECT SUM(total_price) AS total_revenue FROM orders WHERE order_status = 'Delivered'";
$total_sales_result = $conn->query($total_sales_query);
$total_sales = $total_sales_result->fetch_assoc()['total_revenue'] ?? 0;

// Fetch Monthly Sales
$monthly_sales_query = "SELECT SUM(total_price) AS total_revenue 
                        FROM orders 
                        WHERE order_status = 'Delivered' 
                        AND MONTH(order_date) = '$selected_month' 
                        AND YEAR(order_date) = '$selected_year'";
$monthly_sales_result = $conn->query($monthly_sales_query);
$monthly_sales = $monthly_sales_result->fetch_assoc()['total_revenue'] ?? 0;

// Fetch Yearly Sales
$yearly_sales_query = "SELECT SUM(total_price) AS total_revenue 
                       FROM orders 
                       WHERE order_status = 'Delivered' 
                       AND YEAR(order_date) = '$selected_year'";
$yearly_sales_result = $conn->query($yearly_sales_query);
$yearly_sales = $yearly_sales_result->fetch_assoc()['total_revenue'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sidebar.css">
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
        <a href="admin_sales.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
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
        <label for="month">Select Month:</label>
        <select name="month" id="month">
            <option value="01" <?php echo $selected_month == '01' ? 'selected' : ''; ?>>January</option>
            <option value="02" <?php echo $selected_month == '02' ? 'selected' : ''; ?>>February</option>
            <option value="03" <?php echo $selected_month == '03' ? 'selected' : ''; ?>>March</option>
            <option value="04" <?php echo $selected_month == '04' ? 'selected' : ''; ?>>April</option>
            <option value="05" <?php echo $selected_month == '05' ? 'selected' : ''; ?>>May</option>
            <option value="06" <?php echo $selected_month == '06' ? 'selected' : ''; ?>>June</option>
            <option value="07" <?php echo $selected_month == '07' ? 'selected' : ''; ?>>July</option>
            <option value="08" <?php echo $selected_month == '08' ? 'selected' : ''; ?>>August</option>
            <option value="09" <?php echo $selected_month == '09' ? 'selected' : ''; ?>>September</option>
            <option value="10" <?php echo $selected_month == '10' ? 'selected' : ''; ?>>October</option>
            <option value="11" <?php echo $selected_month == '11' ? 'selected' : ''; ?>>November</option>
            <option value="12" <?php echo $selected_month == '12' ? 'selected' : ''; ?>>December</option>
        </select>

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
        var monthlySales = <?php echo json_encode($monthly_sales); ?>;
        var yearlySales = <?php echo json_encode($yearly_sales); ?>;
        var totalSales = <?php echo json_encode($total_sales); ?>;

        // Wait for the DOM to load before running the code
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('salesChart').getContext('2d');
            var salesChart = new Chart(ctx, {
                type: 'bar', // Bar chart
                data: {
                    labels: ['Total Sales', 'Monthly Sales', 'Yearly Sales'], // X-axis labels
                    datasets: [{
                        label: 'Sales Revenue',
                        data: [totalSales, monthlySales, yearlySales], // Y-axis data (Sales revenue for each category)
                        backgroundColor: 'rgba(54, 162, 235, 0.2)', // Color of bars
                        borderColor: 'rgba(54, 162, 235, 1)', // Border color of bars
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true // Start the Y-axis from zero
                        }
                    }
                }
            });
        });
    </script>

</div>



</body>
</html>

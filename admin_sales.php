<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); // Redirect to your login page
    exit();
}

$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Get selected month and year from form input (default to current month and year)
$selected_month = $_GET['month'] ?? date('m');
$selected_year = $_GET['year'] ?? date('Y');

// Fetch Overall Total Sales (All Time) including 'Delivered' and 'Removed' statuses
$total_sales_query = "SELECT SUM(total_price) AS total_revenue 
                      FROM orders 
                      WHERE order_status = 'Delivered' OR order_status = 'Removed'";
// Make sure to include both statuses in the query
$total_sales_result = $conn->query($total_sales_query);
$total_sales = $total_sales_result->fetch_assoc()['total_revenue'] ?? 0;

// Fetch Overall Total Products Sold (All Time) including 'Delivered' and 'Removed' statuses
$total_products_query = "SELECT SUM(order_items_backup.quantity) AS total_products_sold 
                         FROM order_items_backup 
                         JOIN orders ON order_items_backup.order_id = orders.id
                         WHERE orders.order_status = 'Delivered' OR orders.order_status = 'Removed'";
// Include both statuses for the total products sold
$total_products_result = $conn->query($total_products_query);
$total_products_sold = $total_products_result->fetch_assoc()['total_products_sold'] ?? 0;

// Fetch Monthly Sales including 'Delivered' and 'Removed' statuses
$monthly_sales_query = "SELECT SUM(total_price) AS total_revenue 
                        FROM orders 
                        WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                        AND MONTH(order_date) = '$selected_month' 
                        AND YEAR(order_date) = '$selected_year'";
// Ensure the query includes the 'Removed' status for monthly sales
$monthly_sales_result = $conn->query($monthly_sales_query);
$monthly_sales = $monthly_sales_result->fetch_assoc()['total_revenue'] ?? 0;

// Fetch Yearly Sales including 'Delivered' and 'Removed' statuses
$yearly_sales_query = "SELECT SUM(total_price) AS total_revenue 
                       FROM orders 
                       WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                       AND YEAR(order_date) = '$selected_year'";
// Ensure the query includes the 'Removed' status for yearly sales
$yearly_sales_result = $conn->query($yearly_sales_query);
$yearly_sales = $yearly_sales_result->fetch_assoc()['total_revenue'] ?? 0;

// Fetch Completed Orders with Monthly and Yearly Filter (including 'Delivered' and 'Removed' statuses)
$completed_orders_query = "SELECT * FROM orders 
                           WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                           AND MONTH(order_date) = '$selected_month' 
                           AND YEAR(order_date) = '$selected_year' 
                           ORDER BY id DESC";
// Fetch orders marked as either 'Delivered' or 'Removed' for monthly/yearly stats
$completed_orders_result = $conn->query($completed_orders_query);

if (!$completed_orders_result) {
    die("Error fetching completed orders: " . $conn->error);
}

// Fetch Total Canceled Orders Revenue (Only Canceled status)
$canceled_orders_query = "SELECT SUM(total_price) AS canceled_revenue 
                          FROM orders 
                          WHERE order_status = 'Canceled'";
$canceled_orders_result = $conn->query($canceled_orders_query);
$total_canceled_revenue = $canceled_orders_result->fetch_assoc()['canceled_revenue'] ?? 0;

// Fetch Monthly Canceled Orders (Only 'Canceled' status for selected month/year)
$monthly_canceled_query = "SELECT SUM(total_price) AS monthly_canceled_revenue 
                           FROM orders 
                           WHERE order_status = 'Canceled'
                           AND MONTH(order_date) = '$selected_month' 
                           AND YEAR(order_date) = '$selected_year'";
$monthly_canceled_result = $conn->query($monthly_canceled_query);
$monthly_canceled_revenue = $monthly_canceled_result->fetch_assoc()['monthly_canceled_revenue'] ?? 0;


// Pagination setup
$records_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// Count total completed orders
$count_query = "SELECT COUNT(*) AS total 
                FROM orders 
                WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                AND MONTH(order_date) = '$selected_month' 
                AND YEAR(order_date) = '$selected_year'";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'] ?? 0;
$total_pages = ceil($total_records / $records_per_page);

// Modified completed orders query with LIMIT
$completed_orders_query = "SELECT * FROM orders 
                           WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                           AND MONTH(order_date) = '$selected_month' 
                           AND YEAR(order_date) = '$selected_year' 
                           ORDER BY id DESC
                           LIMIT $records_per_page OFFSET $offset";
$completed_orders_result = $conn->query($completed_orders_query);


//chart
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
    <title>Overview Sales</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="sidebar2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
       /* body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }*/

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
            margin-left: 11.5%;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .stat-box {
            padding: 20px;
            background:rgb(92, 95, 93);
            color: white;
            border-radius: 5px;
            width: 29%;
        }

        .btn2{
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    
}

.btn-download {
    background: #17a2b8;
    color: white;
    padding: 6px 14px;
    font-size: 14px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    transition: background 0.3s ease;
}

.btn-download:hover {
    background: #138496;
}

        .chart-container {
            width: 100%;
            max-width: 600px;
            margin: auto;
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
        <a href="logout_admin.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<br><br>

<div class="container">  
    <h2> Overview Sales Statistics </h2>
    


    <!-- Filter Form -->
    <form method="GET" class="filter-form">
        <label for="month">Select Month:</label>
        <select name="month">
            <?php for ($m=1; $m<=12; $m++): ?>
                <option value="<?php echo $m; ?>" <?php if ($m == $selected_month) echo 'selected'; ?>>
                    <?php echo date("F", mktime(0, 0, 0, $m, 1)); ?>
                </option>
            <?php endfor; ?>
        </select>

        <label for="year">Select Year:</label>
        <select name="year">
            <?php for ($y = date('Y'); $y >= 2021; $y--): ?>
                <option value="<?php echo $y; ?>" <?php if ($y == $selected_year) echo 'selected'; ?>><?php echo $y; ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit">Filter</button>
        
    </form>


    <!-- Sales Stats -->
    <div class="stats">
        <!-- Overall Sales -->
        <div class="stat-box" style="background: #343a40;">
            <i class="fas fa-chart-line"></i> <br>
            <strong>Overall Sales:</strong> ₱<?php echo number_format($total_sales, 2); ?>
        </div>

        <!-- Yearly Sales -->
        <div class="stat-box" style="background: #28a745;">
            <i class="fas fa-calendar-alt"></i> <br>
            <strong><?php echo $selected_year; ?> Sales:</strong> ₱<?php echo number_format($yearly_sales, 2); ?>
        </div>

        <!-- Monthly Sales -->
        <div class="stat-box" style="background: #007bff;">
            <i class="fas fa-calendar"></i> <br>
            <strong><?php echo date("F", mktime(0, 0, 0, $selected_month, 1)); ?> Sales:</strong> ₱<?php echo number_format($monthly_sales, 2); ?>
        </div>
    </div>

    <div class="stats">
    <div class="stat-box" style="background: #ffc107;">
    <i class="fas fa-calendar-times"></i> <br>
    <strong><?php echo date("F", mktime(0, 0, 0, $selected_month, 1)); ?> Canceled:</strong>
    ₱<?php echo number_format($monthly_canceled_revenue, 2); ?>
    </div>

    <div class="stat-box" style="background: #dc3545;">
        <i class="fas fa-ban"></i> <br>
        <strong>Total Canceled Orders:</strong> ₱<?php echo number_format($total_canceled_revenue, 2); ?>
    </div>

    <div class="stat-box">
        <i class="fas fa-box"></i> <br>
        <strong>Total Products Sold:</strong> <?php echo number_format($total_products_sold); ?>
    </div>
</div>




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
                        type: 'bar' 
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


<?php
 session_name("admin_session");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}


$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$selected_month = $_GET['month'] ?? date('m');
$selected_year = $_GET['year'] ?? date('Y');

//Overall Total Sales (All Time) including 'Delivered' and 'Removed' statuses
$total_sales_query = "SELECT SUM(total_price) AS total_revenue 
                      FROM orders 
                      WHERE order_status = 'Delivered' OR order_status = 'Removed'";

$total_sales_result = $conn->query($total_sales_query);
$total_sales = $total_sales_result->fetch_assoc()['total_revenue'] ?? 0;

//Overall Total Products Sold (All Time) including 'Delivered' and 'Removed' 
$total_products_query = "SELECT SUM(order_items_backup.quantity) AS total_products_sold 
                         FROM order_items_backup 
                         JOIN orders ON order_items_backup.order_id = orders.id
                         WHERE orders.order_status = 'Delivered' OR orders.order_status = 'Removed'";

$total_products_result = $conn->query($total_products_query);
$total_products_sold = $total_products_result->fetch_assoc()['total_products_sold'] ?? 0;

// Fetch Monthly Sales including 'Delivered' and 'Removed' 
$monthly_sales_query = "SELECT SUM(total_price) AS total_revenue 
                        FROM orders 
                        WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                        AND MONTH(order_date) = '$selected_month' 
                        AND YEAR(order_date) = '$selected_year'";

$monthly_sales_result = $conn->query($monthly_sales_query);
$monthly_sales = $monthly_sales_result->fetch_assoc()['total_revenue'] ?? 0;

// Yearly Sales including 'Delivered' and 'Removed'
$yearly_sales_query = "SELECT SUM(total_price) AS total_revenue 
                       FROM orders 
                       WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                       AND YEAR(order_date) = '$selected_year'";

$yearly_sales_result = $conn->query($yearly_sales_query);
$yearly_sales = $yearly_sales_result->fetch_assoc()['total_revenue'] ?? 0;

//Completed Orders with Monthly and Yearly Filter
$completed_orders_query = "SELECT * FROM orders 
                           WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                           AND MONTH(order_date) = '$selected_month' 
                           AND YEAR(order_date) = '$selected_year' 
                           ORDER BY id DESC";

$completed_orders_result = $conn->query($completed_orders_query);

if (!$completed_orders_result) {
    die("Error fetching completed orders: " . $conn->error);
}

// Total Canceled Orders Revenue 
$canceled_orders_query = "SELECT SUM(total_price) AS canceled_revenue 
                          FROM orders 
                          WHERE order_status = 'Canceled'";
$canceled_orders_result = $conn->query($canceled_orders_query);
$total_canceled_revenue = $canceled_orders_result->fetch_assoc()['canceled_revenue'] ?? 0;

// Fetch Monthly Canceled Orders 
$monthly_canceled_query = "SELECT SUM(total_price) AS monthly_canceled_revenue 
                           FROM orders 
                           WHERE order_status = 'Canceled'
                           AND MONTH(order_date) = '$selected_month' 
                           AND YEAR(order_date) = '$selected_year'";
$monthly_canceled_result = $conn->query($monthly_canceled_query);
$monthly_canceled_revenue = $monthly_canceled_result->fetch_assoc()['monthly_canceled_revenue'] ?? 0;


$records_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

//  total completed orders
$count_query = "SELECT COUNT(*) AS total 
                FROM orders 
                WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                AND MONTH(order_date) = '$selected_month' 
                AND YEAR(order_date) = '$selected_year'";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'] ?? 0;
$total_pages = ceil($total_records / $records_per_page);

// completed orders query with LIMIT
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

//  recent orders
$recent_orders_query = "SELECT id, recipient_name, total_price, order_status, order_date 
                        FROM orders 
                        ORDER BY order_date DESC 
                        LIMIT 5";
$recent_orders_result = $conn->query($recent_orders_query);


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
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>

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
            margin-left: 1%;
            
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
        .chart-container {
            display: inline-block;
    vertical-align: top;
    margin-right: 2%;
        }
        .recent-orders table {
        margin-top: 20px;
         width: 100%;
    border-collapse: collapse;
}

.recent-orders th, .recent-orders td {
    padding: 12px;
    text-align: left;
}

.recent-orders tr:nth-child(even) {
    background-color: #f9f9f9;
}

.recent-orders tr:hover {
    background-color: #f1f1f1;
}

.chart-container, .recent-orders {
    width: 48%; /* Adjust the width to fit the two elements side by side */
}

.recent-orders {
    display: inline-block;
    vertical-align: top;
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

<br>
<div class="container max-w-6xl mx-auto mr-6 mt-1 m-8 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4">Overview Sales Statistics</h2>

  
    <form method="GET" class="flex flex-wrap gap-3 justify-center items-end mb-6">
        <div class="flex flex-col text-sm">
            <label for="month" class="text-gray-600 mb-1">Month:</label>
            <select name="month" id="month" class="border rounded px-2 py-1">
                <?php for ($m=1; $m<=12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php if ($m == $selected_month) echo 'selected'; ?>>
                        <?php echo date("F", mktime(0, 0, 0, $m, 1)); ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="flex flex-col text-sm">
            <label for="year" class="text-gray-600 mb-1">Year:</label>
            <select name="year" id="year" class="border rounded px-2 py-1">
                <?php for ($y = date('Y'); $y >= 2021; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php if ($y == $selected_year) echo 'selected'; ?>>
                        <?php echo $y; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Filter</button>
    </form>


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-white text-base">
        <div class="p-4 bg-gray-800 rounded shadow">
            <i class="fas fa-chart-line text-lg"></i><br>
            <strong>Overall Sales:</strong><br>₱<?php echo number_format($total_sales, 2); ?>
        </div>
        <div class="p-4 bg-green-600 rounded shadow">
            <i class="fas fa-calendar-alt text-lg"></i><br>
            <strong><?php echo $selected_year; ?> Sales:</strong><br>₱<?php echo number_format($yearly_sales, 2); ?>
        </div>
        <div class="p-4 bg-blue-600 rounded shadow">
            <i class="fas fa-calendar text-lg"></i><br>
            <strong><?php echo date("F", mktime(0, 0, 0, $selected_month, 1)); ?> Sales:</strong><br>₱<?php echo number_format($monthly_sales, 2); ?>
        </div>
        <div class="p-4 bg-yellow-500 rounded shadow">
            <i class="fas fa-calendar-times text-lg"></i><br>
            <strong><?php echo date("F", mktime(0, 0, 0, $selected_month, 1)); ?> Canceled:</strong><br>
            ₱<?php echo number_format($monthly_canceled_revenue, 2); ?>
        </div>
        <div class="p-4 bg-red-600 rounded shadow">
            <i class="fas fa-ban text-lg"></i><br>
            <strong>Total Canceled Orders:</strong><br>₱<?php echo number_format($total_canceled_revenue, 2); ?>
        </div>
        <div class="p-4 bg-gray-700 rounded shadow">
            <i class="fas fa-box text-lg"></i><br>
            <strong>Total Products Sold:</strong><br><?php echo number_format($total_products_sold); ?>
        </div>
    </div>

<br>


   
<div style="display: flex; justify-content: space-between;">
        <div class="chart-container">
            <canvas id="salesChart" width="450" height="410"></canvas>
        </div>

    <script>
     
        var monthlySales = <?php echo json_encode($monthly_sales_data); ?>;
        var canceledSales = <?php echo json_encode($canceled_sales_data); ?>;
        var months = <?php echo json_encode($months); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            
            var ctx = document.getElementById('salesChart').getContext('2d');
            var salesChart = new Chart(ctx, {
                type: 'line', 
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Monthly Sales Revenue',
                        data: monthlySales,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        type: 'line' 
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
<br>


<div class="recent-orders -mt-2">
    <h3 class="text-lg font-semibold -mb-2">Recent Orders</h3>
    <table class="table-auto w-full bg-white rounded-lg shadow-md">
        <thead>
            <tr>
                <th class="p-2 bg-blue-600 text-white">Order ID</th>
                <th class="p-2 bg-blue-600 text-white">Customer</th>
                <th class="p-2 bg-blue-600 text-white">Total Price</th>
                <th class="p-2 bg-blue-600 text-white">Status</th>
                <th class="p-2 bg-blue-600 text-white">Order Date</th>
            </tr>   
        </thead>
        <tbody>
            <?php while ($order = $recent_orders_result->fetch_assoc()): ?>
                <tr>
                    <td class="p-2"><?php echo $order['id']; ?></td>
                    <td class="p-2"><?php echo $order['recipient_name']; ?></td>
                    <td class="p-2">₱<?php echo number_format($order['total_price'], 2); ?></td>
                    <td class="p-2"><?php echo $order['order_status']; ?></td>
                    <td class="p-2"><?php echo date("F j, Y", strtotime($order['order_date'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>





</div>





    

</body>
</html>


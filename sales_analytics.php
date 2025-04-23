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

//Overall Total Sales (All Time) 
$total_sales_query = "SELECT SUM(total_price) AS total_revenue 
                      FROM orders 
                      WHERE order_status = 'Delivered' OR order_status = 'Removed'";

$total_sales_result = $conn->query($total_sales_query);
$total_sales = $total_sales_result->fetch_assoc()['total_revenue'] ?? 0;

//Overall Total Products Sold 
$total_products_query = "SELECT SUM(order_items_backup.quantity) AS total_products_sold 
                         FROM order_items_backup 
                         JOIN orders ON order_items_backup.order_id = orders.id
                         WHERE orders.order_status = 'Delivered' OR orders.order_status = 'Removed'";

$total_products_result = $conn->query($total_products_query);
$total_products_sold = $total_products_result->fetch_assoc()['total_products_sold'] ?? 0;

// Fetch Monthly Sales
$monthly_sales_query = "SELECT SUM(total_price) AS total_revenue 
                        FROM orders 
                        WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                        AND MONTH(order_date) = '$selected_month' 
                        AND YEAR(order_date) = '$selected_year'";

$monthly_sales_result = $conn->query($monthly_sales_query);
$monthly_sales = $monthly_sales_result->fetch_assoc()['total_revenue'] ?? 0;

// Yearly Sales including 
$yearly_sales_query = "SELECT SUM(total_price) AS total_revenue 
                       FROM orders 
                       WHERE (order_status = 'Delivered' OR order_status = 'Removed') 
                       AND YEAR(order_date) = '$selected_year'";

$yearly_sales_result = $conn->query($yearly_sales_query);
$yearly_sales = $yearly_sales_result->fetch_assoc()['total_revenue'] ?? 0;

//Completed Orders 
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

$best_selling_query = "
    SELECT p.name AS name, p.image, SUM(oib.quantity) AS total_sold
    FROM order_items_backup oib
    JOIN products p ON oib.product_id = p.id
    JOIN orders o ON oib.order_id = o.id
    WHERE o.order_status = 'Delivered' OR o.order_status = 'Removed'
    GROUP BY p.name, p.image
    ORDER BY total_sold DESC
    LIMIT 5
";
$best_selling_result = $conn->query($best_selling_query);

// Monthly Order Status Breakdown
$monthly_order_status_query = "
    SELECT order_status, COUNT(*) AS total
    FROM orders
    WHERE MONTH(order_date) = '$selected_month'
    AND YEAR(order_date) = '$selected_year'
    GROUP BY order_status
";

$monthly_status_result = $conn->query($monthly_order_status_query);

$order_status_labels = [];
$order_status_counts = [];

while ($row = $monthly_status_result->fetch_assoc()) {
    $order_status_labels[] = $row['order_status'];
    $order_status_counts[] = (int)$row['total'];
}


$all_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$monthly_products_data = array_fill(0, 12, 0); // default all to 0

$query = "
    SELECT MONTH(o.order_date) AS month_num, SUM(oib.quantity) AS total_sold
    FROM order_items_backup oib
    JOIN orders o ON oib.order_id = o.id
    WHERE (o.order_status = 'Delivered' OR o.order_status = 'Removed')
      AND YEAR(o.order_date) = '$selected_year'
    GROUP BY MONTH(o.order_date)
";

$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $month_index = (int)$row['month_num'] - 1; // 0-based index
    $monthly_products_data[$month_index] = (int)$row['total_sold'];
}

$monthly_products_labels = $all_months;




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Analytics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css">
    <link rel="stylesheet" href="sidebar.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }



        /* Main Content Styling */
        .main-content {
            margin-left: 78px;
            padding: 20px;
            background-color: #ecf0f1;
            height: 230vh;
        }

        /* Card Styling */
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            background-color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="admin_sales.php"><i class="fas fa-tachometer-alt"></i><span>Overview</span></a>
        <a href="upload.php"><i class="fas fa-upload"></i><span>Manage Products</span></a>
        <a href="show_users2.php"><i class="fas fa-users"></i><span>Manage Users</span></a>
        <a href="admin_orders.php"><i class="fas fa-box"></i><span>Manage Orders</span></a>
        <a href="sales_analytics.php"><i class="fas fa-chart-line"></i><span>Check Sales</span></a>
        <a href="content_manager2.php"><i class="fas fa-cogs"></i><span>Content Manager</span></a>
        <a href="logout_admin.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2 class="text-xl font-bold mb-6">Sales Analytics Dashboard</h2>

    <!-- Filter Form -->
    <form method="GET" class="mb-6">
        <div class="flex space-x-4">
            <select name="month" class="form-select border rounded p-2">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php if ($m == $selected_month) echo 'selected'; ?>>
                        <?php echo date("F", mktime(0, 0, 0, $m, 1)); ?>
                    </option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-select border rounded p-2">
                <?php for ($y = date('Y'); $y >= 2021; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php if ($y == $selected_year) echo 'selected'; ?>>
                        <?php echo $y; ?>
                    </option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="bg-blue-500 text-white rounded p-2">Filter</button>
        </div>
    </form>

    <!-- Order History Button -->
    <div class="mb-6">
        <a href="dashboard.php" class="bg-green-500 text-white rounded p-2">
            View Order History
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="card">
            <h5 class="font-bold">Overall Sales</h5>
            <p class="text-2xl">₱<?php echo number_format($total_sales, 2); ?></p>
        </div>
        <div class="card">
            <h5 class="font-bold"><?php echo $selected_year; ?> Sales</h5>
            <p class="text-2xl">₱<?php echo number_format($yearly_sales, 2); ?></p>
        </div>
        <div class="card">
            <h5 class="font-bold"><?php echo date("F", mktime(0, 0, 0, $selected_month, 1)); ?> Sales</h5>
            <p class="text-2xl">₱<?php echo number_format($monthly_sales, 2); ?></p>
        </div>
    </div>

    <!-- Charts -->
    <div class="card mt-4" style="width: 1150px;">  <!-- Adjust width as needed -->
        <h5 class="font-bold"> Sales Chart</h5>
        <canvas id="salesChart" width="1200" height="410"></canvas>  <!-- Set canvas width and height -->
    </div>


    <div class="flex gap-6 mt-4">
    <!-- Monthly Products Sold -->
    <div class="card w-3/5 flex flex-col items-center text-center">
        <h5 class="font-bold mb-4">Monthly Products Sold (Quantity)</h5>
        <canvas id="productsPieChart" width="400" height="320"></canvas>
    </div>

    <!-- Best Selling Products -->
    <div class="card w-3/5 flex flex-col items-center text-center">
        <h5 class="font-bold mb-4">Best Selling Products</h5>
        <div class="overflow-x-auto shadow-lg rounded-lg w-full">
            <table class="min-w-full bg-white rounded-lg">
                <thead>
                    <tr class="bg-blue-600 text-white text-left text-sm uppercase tracking-wider">
                        <th class="px-6 py-3 rounded-tl-lg">Image</th>
                        <th class="px-6 py-3">Product Name</th>
                        <th class="px-6 py-3 rounded-tr-lg">Total Sold</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php if ($best_selling_result && $best_selling_result->num_rows > 0): ?>
                        <?php while ($row = $best_selling_result->fetch_assoc()): ?>
                            <tr class="hover:bg-blue-50 transition duration-200">
                                <td class="px-6 py-4 border-b">
                                    <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" class="w-12 h-12 object-cover rounded">
                                </td>
                                <td class="px-6 py-4 border-b font-medium"><?php echo $row['name']; ?></td>
                                <td class="px-6 py-4 border-b"><?php echo number_format($row['total_sold']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No sales data available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




    <script>
    // Chart.js Configuration
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Monthly Sales Revenue',
                    data: <?php echo json_encode($monthly_sales_data); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                }, {
                    label: 'Canceled Orders Revenue',
                    data: <?php echo json_encode($canceled_sales_data); ?>,
                    type: 'bar',
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    tension: 0.10
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

        // Monthly Orders Donut Chart
        const ordersCtx = document.getElementById('ordersDonutChart').getContext('2d');
        const orderStatusCounts = <?php echo json_encode($order_status_counts); ?>;
        const totalOrders = orderStatusCounts.reduce((a, b) => a + b, 0);

        const ordersDonutChart = new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($order_status_labels); ?>,
                datasets: [{
                    data: orderStatusCounts,
                    backgroundColor: [
                        '#4CAF50',  // Delivered
                        '#f39c12',  // Pending
                        '#3498db',  // Processing
                        '#e74c3c',  // Canceled
                        
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const percentage = ((value / totalOrders) * 100).toFixed(1);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    },
                    title: {
                        display: false
                    }
                }
            }
        });
    });


    // Monthly Products Sold Pie Chart
    document.addEventListener('DOMContentLoaded', function() {
    // ... Existing code for other charts ...

    // Monthly Products Sold Bar Chart
    const productsCtx = document.getElementById('productsPieChart').getContext('2d');
    const productsLabels = <?php echo json_encode($monthly_products_labels); ?>; // e.g. ['Jan', 'Feb', 'Mar']
    const productsData = <?php echo json_encode($monthly_products_data); ?>;     // e.g. [50, 75, 100]

    const productsBarChart = new Chart(productsCtx, {
        type: 'bar',
        data: {
            labels: productsLabels,
            datasets: [{
                label: 'Products Sold',
                data: productsData,
                backgroundColor: '#4CAF50',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Sold: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantity Sold'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                }
            }
        }
    });
});


</script>


</body>
</html>
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

// 🔁 PAGINATION VARIABLES FIRST
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// 👇 QUERY WITH LIMIT AFTER
$canceled_orders_query = "SELECT * FROM orders 
                          WHERE order_status = 'Canceled'
                          AND MONTH(order_date) = '$selected_month'
                          AND YEAR(order_date) = '$selected_year'
                          ORDER BY id DESC
                          LIMIT $start_from, $results_per_page";

$canceled_orders_result = $conn->query($canceled_orders_query);


$total_query = "SELECT COUNT(*) AS total FROM orders 
                WHERE order_status = 'Canceled'
                AND MONTH(order_date) = '$selected_month'
                AND YEAR(order_date) = '$selected_year'";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_pages = ceil($total_row['total'] / $results_per_page);





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Canceled Orders</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            margin-left: 12%;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px gray;
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
            background-color: #dc3545;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn2 {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        select, button {
            padding: 8px 10px;
            font-size: 16px;
            margin-right: 10px;
        }
        h2 {
            margin-top: 0;
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

<div class="container">
    <h2>Canceled Orders</h2>

    <form method="GET" class="filter-form">
        <label for="month">Month:</label>
        <select name="month" id="month">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo $m; ?>" <?php if ($m == $selected_month) echo 'selected'; ?>>
                    <?php echo date("F", mktime(0, 0, 0, $m, 1)); ?>
                </option>
            <?php endfor; ?>
        </select>

        <label for="year">Year:</label>
        <select name="year" id="year">
            <?php for ($y = date('Y'); $y >= 2021; $y--): ?>
                <option value="<?php echo $y; ?>" <?php if ($y == $selected_year) echo 'selected'; ?>><?php echo $y; ?></option>
            <?php endfor; ?>
        </select>

        <button type="submit">Filter</button>
    </form>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Email</th>
            <th>Recipient</th>
            <th>Ordered Products</th>
            <th>Total Price</th>
            <th>Order Date</th>
            <th>Status</th>
        </tr>
        <?php if ($canceled_orders_result->num_rows > 0): ?>
            <?php while ($order = $canceled_orders_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                    <td><?php echo htmlspecialchars($order['recipient_name']); ?></td>
                    <td>
                        <a href="order_details.php?order_id=<?php echo $order['id']; ?>">
                            <button class="btn2">View Items</button>
                        </a>
                    </td>
                    <td>₱<?php echo number_format($order['total_price'], 2); ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                    <td style="color: red;"><strong><?php echo $order['order_status']; ?></strong></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No canceled orders found for this month.</td></tr>
        <?php endif; ?>
    </table>
    <?php if ($total_pages > 1): ?>
    <div style="text-align: center; margin-top: 20px;">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?month=<?php echo $selected_month; ?>&year=<?php echo $selected_year; ?>&page=<?php echo $i; ?>" 
               style="margin: 0 5px; padding: 8px 12px; background: <?php echo ($i == $page) ? '#dc3545' : '#eee'; ?>; color: <?php echo ($i == $page) ? 'white' : 'black'; ?>; border-radius: 5px; text-decoration: none;">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>


</div>

</body>
</html>
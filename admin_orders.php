<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, email, recipient_name, phone_number, total_price, order_status, gcash_number, gcash_reference, payment_screenshot 
        FROM orders 
        WHERE order_status IN ('Pending', 'Processing', 'Shipped', 'Canceled')
        ORDER BY id DESC";

$orders_result = $conn->query($sql);
$total_sales = file_get_contents("get_sales.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 20px; }
        h2 { text-align: center; }
        .container { width: auto; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color:rgb(149, 152, 156); color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        .btn { padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .btn-delete { background-color: red; color: white; }
        .btn-complete { background-color: green; color: white; }
        .btn:hover { opacity: 0.8; }
        select { padding: 5px; border-radius: 5px; }
        img { width: 50px; height: auto; border-radius: 5px; cursor: pointer; transition: transform 0.2s; }
        img:hover { transform: scale(1.5); }

        .navbar {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    background-color:rgb(0, 123, 255);
                    padding: 15px;
                    color: white;
                    width: 101%;
                    margin-left: -2%;
                    padding-top: 2%;
                    margin-top: -2%;
            }
            .navbar .logo {
                font-size: 24px;
                font-weight: bold;
            }
            .navbar .nav-links {
                display: flex;
                gap: 20px;
            }
            .navbar a {
                color: white;
                text-decoration: none;
                font-size: 16px;
            }
            .navbar a:hover {
                text-decoration: underline;
            }
            .logout {
                background-color: red;
                padding: 8px 12px;
                border-radius: 5px;
            }
            .logout:hover {
                background-color: darkred;
            }
 
    </style>
</head>
<body>

<div class="navbar">
            <div class="logo">Admin Panel</div>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="upload.php">Manage Products</a>
                <a href="show_users2.php">Manage Users</a>
                <a href="admin_orders.php">Manage Orders</a>
                <a href="admin_sales.php">Check Sales</a>
                <a href="logout.php" class="logout">Logout</a>
            </div>
        </div>

<div class="container">
    <h2>Manage Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Email</th>
            <th>Recipient Name</th>
            <th>Phone Number</th>
            <th>Total Price</th>
            <th>GCash Number</th>
            <th>GCash Reference</th>
            <th>Payment Screenshot</th>
            <th>Ordered Products</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($order = $orders_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo htmlspecialchars($order['email']); ?></td>
                <td><?php echo htmlspecialchars($order['recipient_name']); ?></td>
                <td><?php echo htmlspecialchars($order['phone_number']); ?></td>
                <td>₱<?php echo number_format($order['total_price'], 2); ?></td>
                <td><?php echo htmlspecialchars($order['gcash_number']); ?></td>
                <td><?php echo htmlspecialchars($order['gcash_reference']); ?></td>
                <td>
                    <?php if (!empty($order['payment_screenshot'])) { ?>
                        <img src="<?php echo htmlspecialchars($order['payment_screenshot']); ?>" onclick="openModal(this.src)">
                    <?php } else { echo "No Screenshot"; } ?>
                </td>
                <td>
                    <a href="order_details.php?order_id=<?php echo $order['id']; ?>">
                        <button class="btn">View Items</button>
                    </a>
                </td>
                <td><?php echo $order['order_status']; ?></td>
                <td>
                    <form action="update_order.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="order_status" onchange="this.form.submit()">
                            <option value="Pending" <?php if ($order['order_status'] == "Pending") echo "selected"; ?>>Pending</option>
                            <option value="Processing" <?php if ($order['order_status'] == "Processing") echo "selected"; ?>>Processing</option>
                            <option value="Shipped" <?php if ($order['order_status'] == "Shipped") echo "selected"; ?>>Shipped</option>
                            <option value="Canceled" <?php if ($order['order_status'] == "Canceled") echo "selected"; ?>>Canceled</option>
                        </select>
                    </form>
                    <form action="update_order.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" name="mark_delivered" class="btn btn-complete">Mark as Delivered</button>
                    </form>
                    <form action="delete_order.php" method="POST" onsubmit="return confirm('Are you sure you want to remove this order?');">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" name="delete_order" class="btn btn-delete">Remove</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

</div>

<script>
    function openModal(src) {
        document.getElementById("modalImage").src = src;
        document.getElementById("imageModal").style.display = "block";
    }
    function closeModal() {
        document.getElementById("imageModal").style.display = "none";
    }
</script>
</body>
</html>

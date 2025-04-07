<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, email, recipient_name, phone_number, total_price, order_status, gcash_number, gcash_reference, payment_screenshot
        FROM orders 
        WHERE is_removed = 0 AND order_status != 'Delivered'
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
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* General Body Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 20px;
}

/* Header Styling */
h2 {
    text-align: center;
}

/* Container Styling */
.container {
    width: 93%;
    margin-left: 5%;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Table Styling */
table {
    width: 10%;
    border-collapse: collapse;
    margin-top: 20px;
}

/* Table Header and Data Styling */
th, td {
    border: 1px solid #ddd;
    padding: 10px;  
    text-align: center
}

th {
    background-color: rgb(149, 152, 156);
    color: white;
}

/* Table Row Styling */
tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}

/* Button Styling */
.btn {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.btn-delete {
    background-color: red;
    color: white;
}

.btn-complete {
    background-color: green;
    color: white;
}

.btn:hover {
    opacity: 0.8;
}

/* Select Element Styling */
select {
    padding: 5px;
    border-radius: 5px;
}

/* Image Styling */
img {
    width: 50px;
    height: auto;
    border-radius: 5px;
    cursor: pointer;
    transition: transform 0.2s;
}

img:hover {
    transform: scale(1.5);
}

.modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
        padding-top: 60px;
    }
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 10px;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
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
                <td><?php echo !empty($order['gcash_number']) ? htmlspecialchars($order['gcash_number']) : 'N/A'; ?></td>
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
                <td>
    <?php echo $order['order_status']; ?>
    <?php if ($order['order_status'] == 'Canceled' && !empty($order['cancellation_reason'])) { ?>
        <button class="btn" onclick="openCancellationReasonModal('<?php echo htmlspecialchars($order['cancellation_reason']); ?>')">View Cancellation Reason</button>
    <?php } ?>
</td>
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
                    <br>
                    <form action="update_order.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" name="mark_delivered" class="btn btn-complete">Delivered</button>
                    </form>
                    <br>
                    <form action="delete_order.php" method="POST" onsubmit="return confirm('Are you sure you want to remove this order?');">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" name="delete_order" class="btn btn-delete">Remove</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- Modal for Cancellation Reason -->
<div id="cancellationReasonModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeCancellationReasonModal()">&times;</span>
        <h3>Cancellation Reason</h3>
        <p id="cancellationReasonText"></p>
    </div>
</div>


</div>

<script>
    function openModal(src) {
        document.getElementById("modalImage").src = src;
        document.getElementById("imageModal").style.display = "block";
    }
    function closeModal() {
        document.getElementById("imageModal").style.display = "none";
    }

    function openCancellationReasonModal(reason) {
        document.getElementById("cancellationReasonText").innerText = reason;
        document.getElementById("cancellationReasonModal").style.display = "block";
    }

    function closeCancellationReasonModal() {
        document.getElementById("cancellationReasonModal").style.display = "none";
    }
</script>


</body>
</html>

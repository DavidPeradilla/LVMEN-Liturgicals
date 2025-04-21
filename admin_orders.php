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

$selected_status = isset($_GET['status']) ? $_GET['status'] : '';

$status_filter = '';
if (!empty($selected_status)) {
    $status_filter = "AND order_status = '" . $conn->real_escape_string($selected_status) . "'";
}


$sql = "SELECT id, email, recipient_name, phone_number, total_price, order_status, gcash_number, gcash_reference, payment_screenshot, cancellation_reason
        FROM orders 
        WHERE is_removed = 0 AND order_status != 'Delivered' $status_filter
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
        }

        h2 {
            text-align: center;
        }

        .container {
            width: 93%;
            margin-left: 5%;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 60%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 7px;  
            text-align: center;
        }

        th {
            background-color: rgb(149, 152, 156);
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

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

        select {
            padding: 5px;
            border-radius: 5px;
        }

        img {
            width: 50px;
            height: auto;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.2s;
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

        .close:hover
         {
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
        <a href="admin_sales.php"><i class="fas fa-tachometer-alt"></i><span>Overview</span></a>
        <a href="upload.php"><i class="fas fa-upload"></i><span>Manage Products</span></a>
        <a href="show_users2.php"><i class="fas fa-users"></i><span>Manage Users</span></a>
        <a href="admin_orders.php"><i class="fas fa-box"></i><span>Manage Orders</span></a>
        <a href="sales_analytics.php"><i class="fas fa-chart-line"></i><span>Check Sales</span></a>
        <a href="content_manager2.php"><i class="fas fa-cogs"></i><span>Content Manager</span></a>
        <a href="logout_admin.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
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
        <form method="GET" style="margin-bottom: 20px;">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="Pending" <?php if ($selected_status == "Pending") echo "selected"; ?>>Pending</option>
                <option value="Processing" <?php if ($selected_status == "Processing") echo "selected"; ?>>Processing</option>
                <option value="Shipped" <?php if ($selected_status == "Shipped") echo "selected"; ?>>Shipped</option>
                <option value="Canceled" <?php if ($selected_status == "Canceled") echo "selected"; ?>>Canceled</option>
            </select>
        </form>

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
    <br>
    <?php if ($order['order_status'] == 'Canceled') { ?>
        <button class="btn" onclick="openCancellationReasonModal(<?php echo $order['id']; ?>, '<?php echo htmlspecialchars($order['cancellation_reason']); ?>')">Edit Cancellation Reason</button>
    <?php } ?>
</td>


                <td>
    <!-- Update Status Dropdown -->
    <form action="update_order.php" method="POST" style="margin-bottom: 10px;">
        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
        <select name="order_status" onchange="this.form.submit()">
            <option value="Pending" <?php if ($order['order_status'] == "Pending") echo "selected"; ?>>Pending</option>
            <option value="Processing" <?php if ($order['order_status'] == "Processing") echo "selected"; ?>>Processing</option>
            <option value="Shipped" <?php if ($order['order_status'] == "Shipped") echo "selected"; ?>>Shipped</option>
            <option value="Canceled" <?php if ($order['order_status'] == "Canceled") echo "selected"; ?>>Canceled</option>
        </select>
    </form>

    <!-- Deliver Button (only if not canceled) -->
    <?php if ($order['order_status'] != 'Canceled') { ?>
        <form action="update_order.php" method="POST" style="margin-bottom: 10px;">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <button type="submit" name="mark_delivered" class="btn btn-complete">Delivered</button>
        </form>
    <?php } ?>

    <!-- Remove Button (always show) -->
    <form action="delete_order.php" method="POST" onsubmit="return confirm('Are you sure you want to remove this order?');">
        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
        <button type="submit" name="delete_order" class="btn btn-delete">Remove</button>
    </form>
</td>

            </tr>
        <?php } ?>
    </table>
</div>

<!-- Modal for Full Image -->
<div id="imageModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" style="width: 100%;">
    </div>
</div>

<!-- Modal for Cancellation Reason -->
<div id="cancelModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Edit Cancellation Reason</h3>
        <form id="cancelForm" method="POST" action="cancel_order.php">
            <input type="hidden" id="order_id" name="order_id" value="">
            <textarea name="reason" required placeholder="Enter your reason for cancellation" rows="4" style="width: 100%;"></textarea>
            <br><br>
            <button type="submit">Submit Cancellation</button>
        </form>
    </div>
</div>

<script>
    function openModal(src) {
        document.getElementById("modalImage").src = src;
        document.getElementById("imageModal").style.display = "block";
    }
    function openCancellationReasonModal(orderId, currentReason) {
    console.log('Opening modal for orderId:', orderId);  // Debugging line
    document.getElementById("order_id").value = orderId;
    document.getElementsByName("reason")[0].value = currentReason;
    document.getElementById("cancelModal").style.display = "block";
}

function closeModal() {
    document.getElementById("imageModal").style.display = "none";
    document.getElementById("cancelModal").style.display = "none";
}


    // Close the modal if the user clicks outside of it
    window.onclick = function(event) {
        if (event.target == document.getElementById("cancelModal")) {
            closeModal();
        }
    };
</script>

</body>
</html>

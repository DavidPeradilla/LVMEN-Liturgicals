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
    <link rel="stylesheet" href="sidebar2.css">
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
            width: 80%;
            margin-left: 6%;
            max-width: 1200px;
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding-right: 14%;
        }

        table {
            width: 115%;
            border-collapse: collapse;
            margin-top: 10px;
            
        }

        th, td {
            border: 1px solid #ddd;
            padding: 5px;  
            text-align: center;
            font-size: 14.2px;
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
            padding: 6px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
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
            padding: 3px;
            border-radius: 5px;
            font-size: 12px;
        }

        img {
            width: 40px;
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
            padding: 15px;
            border: 1px solid #888;
            width: 60%;
            max-width: 400px;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover {
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
        <a href="admin_orders.php"><i class="fas fa-box"></i><span>Manage Orders</span></a>
        <a href="sales_analytics.php"><i class="fas fa-chart-line"></i><span>Check Sales</span></a>
        <a href="content_manager2.php"><i class="fas fa-cogs"></i><span>Content Manager</span></a>
        <a href="show_users2.php"><i class="fas fa-users"></i><span>Customers</span></a>
        <a href="logout_admin.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<div class="container">
    <h2>Manage Orders</h2>

    <form method="GET" style="margin-bottom: 10px;">
        <label for="status">Filter by Status:</label>
        <select name="status" id="status" onchange="this.form.submit()">
            <option value="">All</option>
            <?php
            $statuses = ['Pending', 'Processing', 'Shipped', 'Canceled'];
            foreach ($statuses as $status) {
                $selected = ($selected_status == $status) ? 'selected' : '';
                echo "<option value=\"$status\" $selected>$status</option>";
            }
            ?>
        </select>
    </form>

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
                <td><?= $order['id'] ?></td>
                <td><?= htmlspecialchars($order['email']) ?></td>
                <td><?= htmlspecialchars($order['recipient_name']) ?></td>
                <td><?= htmlspecialchars($order['phone_number']) ?></td>
                <td>₱<?= number_format($order['total_price'], 2) ?></td>
                <td><?= !empty($order['gcash_number']) ? htmlspecialchars($order['gcash_number']) : 'N/A' ?></td>
                <td><?= htmlspecialchars($order['gcash_reference']) ?></td>
                <td>
                    <?php if (!empty($order['payment_screenshot'])): ?>
                        <img src="<?= htmlspecialchars($order['payment_screenshot']) ?>" onclick="openModal(this.src)">
                    <?php else: ?>
                        No Screenshot
                    <?php endif; ?>
                </td>
                <td>
                    <a href="order_details.php?order_id=<?= $order['id'] ?>">
                        <button class="btn">View Items</button>
                    </a>
                </td>
                <td>
                    <?= $order['order_status'] ?>
                    <?php if ($order['order_status'] == 'Canceled'): ?>
                        <br>
                        <button class="btn" onclick="openCancellationReasonModal(<?= $order['id'] ?>, `<?= htmlspecialchars($order['cancellation_reason']) ?>`)"> Reason</button>
                    <?php endif; ?>
                </td>
                <td>
                    <form action="update_order.php" method="POST" style="margin-bottom: 5px;">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="order_status" onchange="this.form.submit()">
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= $status ?>" <?= ($order['order_status'] == $status) ? 'selected' : '' ?>><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <?php if ($order['order_status'] != 'Canceled'): ?>
                        <form action="update_order.php" method="POST" style="margin-bottom: 5px;" onsubmit="return confirm('Are you sure you want to mark this order?');">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <button type="submit" name="mark_delivered" class="btn btn-complete">Mark Delivered</button>
                        </form>
                    <?php endif; ?>
                    <form action="delete_order.php" method="POST" onsubmit="return confirm('Are you sure you want to remove this order?');">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" class="btn btn-delete">Remove</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<!-- Screenshot Modal -->
<div id="imageModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" style="width: 100%;">
    </div>
</div>

<!-- Cancellation Reason Modal -->
<div id="cancelModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('cancelModal')">&times;</span>
        <h3>Edit Cancellation Reason</h3>
        <form id="cancelForm" method="POST" action="cancel_order2.php">
            <input type="hidden" id="order_id" name="order_id" value="">
            <textarea name="reason" required placeholder="Enter your reason" rows="4" style="width: 100%;"></textarea>
            <br><br>
            <button type="submit">Update Reason</button>
        </form>
    </div>
</div>

<script>
    function openModal(src) {
        document.getElementById("modalImage").src = src;
        document.getElementById("imageModal").style.display = "block";
    }

    function openCancellationReasonModal(orderId, reason) {
        document.getElementById("order_id").value = orderId;
        document.querySelector("#cancelForm textarea[name='reason']").value = reason;
        document.getElementById("cancelModal").style.display = "block";
    }

    function closeModal(modalId = 'imageModal') {
        document.getElementById(modalId).style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    };
</script>


</body>
</html>

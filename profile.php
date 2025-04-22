<?php
session_name("user_session"); // Only if you used this in your login/logout files
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['cancel_message'])) {
    $message = $_SESSION['cancel_message'];
    unset($_SESSION['cancel_message']); // Clear the message after displaying it
}

$email = $_SESSION['email'];

// Fetch user details
$stmt = $conn->prepare("SELECT first_name, last_name, email, address, contact_number FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

// Fetch order history
$stmt = $conn->prepare("SELECT id, total_price, order_status, cancellation_reason FROM orders WHERE email = ? ORDER BY id DESC");
$stmt->bind_param("s", $email);
$stmt->execute();
$orders_result = $stmt->get_result();


// Pagination setup
$ordersPerPage = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $ordersPerPage;

// Get total orders
$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE email = ?");
$count_stmt->bind_param("s", $email);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$totalOrders = $count_result->fetch_assoc()['total'];
$count_stmt->close();

$totalPages = ceil($totalOrders / $ordersPerPage);

// Fetch paginated orders
$stmt = $conn->prepare("SELECT id, total_price, order_status, cancellation_reason FROM orders WHERE email = ? ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->bind_param("sii", $email, $ordersPerPage, $offset);
$stmt->execute();
$orders_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="LVMEN.css">
    <link rel="stylesheet" type="text/css" href="navbar3.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #222;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background: rgb(141, 138, 136);
            color: white;
            font-weight: 500;
        }
        td {
            border-bottom: 1px solid #ddd;
        }
        .track-btn, .cancel-btn {
            background: rgb(141, 138, 136);
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 10px;
            transition: 0.3s;
            font-size: 14px;
        }
        .track-btn:hover, .cancel-btn:hover {
            background: #005ecb;
        }
        .edit-profile-btn {
            background: #005ecb;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 10px;
            display: block;
            width: 200px;
            margin: 20px auto;
            text-align: center;
            
        }
 
        .logout-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007AFF;
            font-weight: 600;
            font-size: 16px;
        }
        .logout-link:hover {
            text-decoration: underline;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 400px;
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

        .modal button {
            background-color: #005ecb;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
        }


        .notification {
    background-color: #4CAF50;
    color: white;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    text-align: center;
}
 
    </style>
</head>
<body>
<?php if (isset($message)): ?>
    <div class="notification" style="background-color: #28a745; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- NAVBAR -->
<header>
  <a href="LVMEN.php">
    <img class="logo" src="Img/LVMEN Logo.jpg" style="margin-left: 1%;" width="80px" height="70px">
  </a>

  <nav class="navbar">
    <ul class="nav-links">
      <li><a href="LVMEN.php"> HOMEPAGE </a></li>
      <li><a href="AboutUs.php"> ABOUT US </a></li>
      <li><a href="user_products.php"> CATALOG </a></li>
      <li><a href="Contact.php"> CONTACT US </a></li>
      <li><a href="FAQs.php"> FAQs </a></li>

      <!-- Show profile link if logged in -->
      <li><a href="profile.php"><i class="fas fa-user"></i> </a></li>

      <!-- Show cart link -->
      <li><a href="view_cart.php" class="cart-link">
        <i class="fas fa-shopping-cart"></i>
      </a></li>

      <!-- Hide login button only if user is logged in -->
      <?php if (!isset($_SESSION['email'])): ?>
        <li><a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> LOGIN</a></li>
      <?php endif; ?>
      
    </ul>
  </nav>
</header>
<!-- END -->


<br><br><br><br><br>
<div class="mx-auto mt-10 max-w-3xl bg-white p-4 rounded-xl shadow-lg">

    <h2 style=" font-weight: bold;">My Profile</h2>
    <table>
        <tr><th>First Name</th><td><?php echo htmlspecialchars($user['first_name']); ?></td></tr>
        <tr><th>Last Name</th><td><?php echo htmlspecialchars($user['last_name']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
        <tr><th>Address</th><td><?php echo htmlspecialchars($user['address']); ?></td></tr>
        <tr><th>Contact Number</th><td><?php echo htmlspecialchars($user['contact_number']); ?></td></tr>
    </table>
    <a href="edit_profile.php" class="edit-profile-btn">Edit Profile</a>

    <h2 style=" font-weight: bold;">My Order History</h2>
    <?php if ($orders_result->num_rows > 0) { ?>
    <table>
    <tr><th>Order ID</th><th>Total Price</th><th>Status</th><th>Reason</th><th>Track</th><th>Action</th></tr>
    <?php while ($order = $orders_result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $order['id']; ?></td>
        <td>₱<?php echo number_format($order['total_price'], 2); ?></td>
        <td><?php echo htmlspecialchars($order['order_status']) ?: 'Pending'; ?></td>
        <td>
    <?php
        if ($order['order_status'] == 'Canceled') {
            echo '<button class="cancel-reason-btn bg-gray-600 text-white px-2 py-2 rounded-lg hover:bg-gray-700 transition" onclick="openCancellationReasonModal(' . $order['id'] . ')">View Reason</button>';
        } else {
            echo '-';
        }
    ?>

</td>
        <td>
            <form method="POST" action="order_tracking.php">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <button type="submit" class="track-btn">Track</button>
            </form>
        </td>
        <td>
            <?php if ($order['order_status'] == 'Pending') { ?>
                <button class="cancel-btn" onclick="openModal(<?php echo $order['id']; ?>)">Cancel</button>
            <?php } ?>
        </td>
    </tr>
<?php } ?>
    </table>
    <?php } else { ?>
        <p>No orders found.</p>
    <?php } ?>

    <div style="text-align: center; margin-top: 20px;">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" style="margin-right: 10px;">&laquo; Previous</a>
    <?php endif; ?>

    Page <?php echo $page; ?> of <?php echo $totalPages; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>" style="margin-left: 10px;">Next &raquo;</a>
    <?php endif; ?>

    <a href="logout.php" class="logout-link">Logout</a>

</div>


</div>

<!-- Modal -->
<div id="cancelModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Enter your cancellation reason</h3>
        <form id="cancelForm" method="POST" action="cancel_order.php">
            <input type="hidden" id="order_id" name="order_id" value="">
            <textarea name="reason" required placeholder="Enter your reason for cancellation" rows="4" style="width: 100%;"></textarea>
            <br><br>
            <button type="submit">Submit Cancellation</button>
        </form>
    </div>
</div>

<!-- Modal for Cancellation Reason -->
<div id="cancellationReasonModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCancellationReasonModal()">&times;</span>
        <h3>Cancellation Reason</h3>
        <p id="cancellationReasonText"></p>
    </div>
</div>




<script>
    // Modal functionality
    function openModal(orderId) {
        document.getElementById("order_id").value = orderId;
        document.getElementById("cancelModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("cancelModal").style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById("cancelModal")) {
            closeModal();
        }
    }

    // Open the cancellation reason modal
function openCancellationReasonModal(orderId) {
    // Use AJAX to fetch the cancellation reason for the order ID
    $.ajax({
        url: 'cancellation_reason.php',
        type: 'POST',
        data: { order_id: orderId },
        success: function(response) {
            // Set the cancellation reason in the modal
            $('#cancellationReasonText').text(response);
            // Show the modal
            document.getElementById("cancellationReasonModal").style.display = "block";
        }
    });
}

// Close the modal
function closeCancellationReasonModal() {
    document.getElementById("cancellationReasonModal").style.display = "none";
}

// Close the modal if user clicks outside of it
window.onclick = function(event) {
    if (event.target == document.getElementById("cancellationReasonModal")) {
        closeCancellationReasonModal();
    }
}



    
</script>

<?php $conn->close(); ?>

</body>
</html>

<?php
session_name("user_session"); // Only if you're using a custom session name consistently
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$order = null;
$order_items = [];
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure the order_id is a valid integer
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

    if ($order_id > 0) {
        // Fetch order details and order items
        $stmt = $conn->prepare("
        SELECT o.id, o.recipient_name, o.email, o.total_price, o.order_status, 
               o.courier_name, o.tracking_link, o.status_updated_at,
               oi.product_id, oi.quantity, oi.price, p.name 
        FROM orders o
        LEFT JOIN order_items_backup oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.id = ?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Collect order details and products
            $order_items = [];
            while ($row = $result->fetch_assoc()) {
                $order_items[] = $row;
            }

            // Store order details and items in session
                       // Store order details and items in session
                       $_SESSION['order'] = $order_items[0]; // Use the first item for order details
                       $_SESSION['order_items'] = $order_items;
           
                       // Redirect after POST to prevent form resubmission
                       header("Location: " . $_SERVER['PHP_SELF']);
                       exit();
           
        } else {
            $error_message = "Order not found. Please check your Order ID.";
        }

        $stmt->close();
    } else {
        $error_message = "Invalid Order ID.";
    }
}

// Retrieve order data from session if available
$order = isset($_SESSION['order']) ? $_SESSION['order'] : null;
$order_items = isset($_SESSION['order_items']) ? $_SESSION['order_items'] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Track Your Order</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="LVMEN.css">
    <link rel="stylesheet" type="text/css" href="navbar3.css">
    <style>
        body {
    font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
    text-align: center;
    color: #333;
}

.container {
    width: 90%;
    max-width: 600px;
    margin: 40px auto;
    background-color: white;
    padding: 25px 30px;
    border-radius: 15px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}

h2 {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden; /* helps with border-radius clipping */
}

th, td {
    padding: 14px 16px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background: rgb(141, 138, 136);
    color: white;
    font-weight: 600;
}

tr:last-child td {
    border-bottom: none;
}

tr:nth-child(even) td {
    background: #f2f2f2;
}

.status {
    font-weight: bold;
    color: #007aff;
}

.back-link {
    display: inline-block;
    margin-top: 25px;
    text-decoration: none;
    color: #007aff;
    padding: 8px 16px;
    border: 2px solidrgb(0, 0, 0);
    border-radius: 8px;
    transition: all 0.3s ease;
}


    </style>
</head>
<body>

<!-- NAVBAR -->
<header>
  <a href="LVMEN.php">
    <img class="logo" src="Img/LVMEN Logo.jpg" style="margin-left: -88%;" width="80px" height="70px">
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

<br><br><br><br>

<div class="container">
    <h2>Track Your Order</h2>

    <?php if ($order): ?>
        <h3>Order Details</h3>
        <table>
            <tr><th>Order ID</th><td><?php echo htmlspecialchars($order['id']); ?></td></tr>
            <tr><th>Recipient Name</th><td><?php echo htmlspecialchars($order['recipient_name']); ?></td></tr>
            <tr><th>Email</th><td><?php echo htmlspecialchars($order['email']); ?></td></tr>
            <tr><th>Total Price</th><td>₱<?php echo number_format($order['total_price'], 2); ?></td></tr>
            <tr><th>Status</th><td class="status"><?php echo htmlspecialchars($order['order_status']); ?></td></tr>
            <tr><th>Status Updated At</th><td><?php echo date("F j, Y, g:i a", strtotime($order['status_updated_at'])); ?></td></tr>
            <tr><th>Courier</th><td><?php echo htmlspecialchars($order['courier_name'] ?? 'Not Assigned'); ?></td></tr>
            <tr>
                <th>Tracking Link</th>
                <td>
                    <?php if (!empty($order['tracking_link'])): ?>
                        <a href="<?php echo htmlspecialchars($order['tracking_link']); ?>" target="_blank">Track Package</a>
                    <?php else: ?>
                        Not Available
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <h3>Products Ordered</h3>
        <table>
            <tr><th>Product Name</th><th>Quantity</th><th>Price</th></tr>
            <?php foreach ($order_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($error_message): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <a href="profile.php" class="back-link">Back to Profile</a>
</div>

</body>
</html>

<?php $conn->close(); ?>

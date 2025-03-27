<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user details
$stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Fetch order history
$stmt = $conn->prepare("SELECT id, total_price, order_status FROM orders WHERE email = ? ORDER BY id DESC");
$stmt->bind_param("s", $email);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="LVMEN.css">
    <link rel="stylesheet" type="text/css" href="navbar2.css">
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
            background:rgb(141, 138, 136);
            color: white;
            font-weight: 500;
        }
        td {
            border-bottom: 1px solid #ddd;
        }
        .track-btn {
            background:rgb(141, 138, 136);
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 10px;
            transition: 0.3s;
            font-size: 14px;
        }
        .track-btn:hover {
            background: #005ecb;
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
    </style>
</head>
<body>

<!-- NAVBAR -->
<header> 
<a href="LVMEN.php"> <img class="logo" src="Img/LVMEN Logo.jpg" style="margin-left: 1%;" width="80px" height="70px"></a>
  <nav class="navbar"> 
     <ul class="nav-links">
     <a href="LVMEN.php"> <li> HOMEPAGE </li> </a>  
      <a href="AboutUs.php"> <li> ABOUT US  </li> </a>
      <a href="user_products.php"> <li> CATALOG </li> </a>
      <a href="Contact.php"> <li> CONTACT US </li> </a>
      <a href="FAQs.php"> <li> FAQs </li> </a>
      <a href="profile.php"> Profile </a>


      <?php if (isset($_SESSION['email'])): ?>
      <a href="logout.php" class="login-btn"> <li> LOGOUT </li> </a>
      <?php else: ?>
      <a href="login.php" class="login-btn"> <li> LOGIN </li> </a>
      <?php endif; ?>
      <a href="view_cart.php" class="cart-link">🛒</a>
     </ul>
  </nav> 
</header>
<!-- END -->
<br><br><br>
<br><br>

<div class="container">
    <h2>My Profile</h2>
    
    <table>
        <tr>
            <th>First Name</th>
            <td><?php echo htmlspecialchars($user['first_name']); ?></td>
        </tr>
        <tr>
            <th>Last Name</th>
            <td><?php echo htmlspecialchars($user['last_name']); ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
        </tr>
    </table>

    <h2>My Order History</h2>
    
    <table>
        <tr>
            <th>Order ID</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Track Order</th>
        </tr>
        <?php while ($order = $orders_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                <td><?php echo !empty($order['order_status']) ? htmlspecialchars($order['order_status']) : 'Pending'; ?></td>

                <td>
                    <form method="POST" action="order_tracking.php">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" class="track-btn">Track</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

   
</div>

</body>
</html>

<?php $conn->close(); ?>

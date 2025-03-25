<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email'])) {
    die("You need to log in to checkout.");
}

$email = $_SESSION['email'];

// Start transaction
$conn->begin_transaction();

try {
    // Fetch cart items
    $sql = "SELECT cart.product_id, products.name AS product_name, products.price, cart.quantity, 
                   (products.price * cart.quantity) AS total_price 
            FROM cart 
            JOIN products ON cart.product_id = products.id 
            WHERE cart.email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart_items = [];
    $total_order_price = 0;

    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_order_price += $row['total_price'];
    }
    $stmt->close();

    // If cart is empty, stop
    if (count($cart_items) == 0) {
        die("Your cart is empty. <a href='user_products.php'>Shop Now</a>");
    }

    // Create a new order with placeholders for recipient info (filled later)
    $order_status = "Pending";
    $order_sql = "INSERT INTO orders (email, total_price, order_status, recipient_name, phone_number, street, unit_floor) 
                  VALUES (?, ?, ?, '', '', '', '')"; // Empty values will be updated later
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("sds", $email, $total_order_price, $order_status);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert items into order_items
    $insert_item_sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
    foreach ($cart_items as $item) {
        $stmt = $conn->prepare($insert_item_sql);
        $stmt->bind_param("iisid", $order_id, $item['product_id'], $item['product_name'], $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    // Clear cart
    $clear_cart_sql = "DELETE FROM cart WHERE email = ?";
    $stmt = $conn->prepare($clear_cart_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();

    // Commit transaction
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    die("Error processing order: " . $e->getMessage());
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" type="text/css" href="LVMEN.css">
</head>
<body>

<h2>Checkout</h2>

<form action="gcash_payment.php" method="POST">
    <label>Email Address:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly><br>

    <label>Recipient's Name:</label>
    <input type="text" name="recipient_name" required><br>

    <label>Street/Building Name:</label>
    <input type="text" name="street" required><br>

    <label>Unit/Floor (Optional):</label>
    <input type="text" name="unit_floor" placeholder="Optional"><br>

    <label>Phone Number:</label>
    <input type="text" name="phone_number" required><br>

    <h3>Order Summary</h3>
    <table border="1">
        <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>
        <?php foreach ($cart_items as $item) { ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>₱<?php echo number_format($item['total_price'], 2); ?></td>
            </tr>
        <?php } ?>
    </table>
    
    <h3>Payment Method</h3>
    <input type="text" value="GCash" disabled>
    <input type="hidden" name="payment_method" value="GCash">   
    <label>GCash Number:</label>
    <input type="text" name="gcash_number" required>

    <h3>Total Price: ₱<?php echo number_format($total_order_price, 2); ?></h3>
    <input type="hidden" name="total_price" value="<?php echo $total_order_price; ?>">
    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

    <button type="submit">Proceed to GCash Payment</button>
</form>

</body>
</html>

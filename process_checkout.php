<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email'])) {
    die("You need to log in to place an order.");
}

$email = $_SESSION['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient_name = $_POST['recipient_name'];
    $address = $_POST['street'];
    $unit = !empty($_POST['unit_floor']) ? $_POST['unit'] : 'N/A';
    $phone = $_POST['phone_number'];
    $total_price = $_POST['total_price'];

    // Insert new order
    $sql = "INSERT INTO orders (email, recipient_name, total_price, payment_method, gcash_number, order_status) 
        VALUES (?, ?, ?, 'GCash', ?, 'Pending')";
    
    $gcash_number = $_POST['gcash_number'];
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $_SESSION['email'], $_POST['recipient_name'], $_POST['total_price'], $gcash_number);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Get new order ID

    // Move cart items to order_items
    $cart_sql = "SELECT product_id, quantity FROM cart WHERE email = ?";
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $cart_result = $stmt->get_result();

    while ($cart_item = $cart_result->fetch_assoc()) {
        $product_id = $cart_item['product_id'];
        $quantity = $cart_item['quantity'];

        // Fetch product price
        $price_query = "SELECT price FROM products WHERE id = ?";
        $price_stmt = $conn->prepare($price_query);
        $price_stmt->bind_param("i", $product_id);
        $price_stmt->execute();
        $price_result = $price_stmt->get_result();
        $product = $price_result->fetch_assoc();
        $price = $product['price'];

        // Insert into order_items
        $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                           VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($order_item_sql);
        $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
        $stmt->execute();
    }

    // Clear user's cart
    $delete_cart_sql = "DELETE FROM cart WHERE email = ?";
    $stmt = $conn->prepare($delete_cart_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Redirect to confirmation page
    header("Location: order_confirmation.php");
    exit();
}
?>

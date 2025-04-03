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

// Fetch user details
$user_sql = "SELECT first_name, last_name, address, contact_number FROM users WHERE email = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();


if (!$user) {
    die("User details not found.");
}

// Fetch cart items from session
if (!isset($_SESSION['checkout'])) {
    die("Your cart is empty. <a href='user_products.php'>Shop Now</a>");
}

$cart_items = $_SESSION['checkout']['cart_items'];
$total_price = $_SESSION['checkout']['total_price'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gcash_number = $_POST['gcash_number']; // Get the GCash number from the form
    $gcash_reference = $_POST['gcash_reference'];

    // ✅ Validate payment details
    if (empty($gcash_number) || empty($gcash_reference)) {
        die("Invalid GCash payment details.");
    }

    // ✅ Handle payment screenshot upload
    $screenshot_path = null;
    if (isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] == 0) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["payment_screenshot"]["name"]);
        $target_file = $target_dir . $file_name;

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES["payment_screenshot"]["type"], $allowed_types)) {
            die("Invalid file type. Only JPG, PNG, and GIF allowed.");
        }

        if (move_uploaded_file($_FILES["payment_screenshot"]["tmp_name"], $target_file)) {
            $screenshot_path = $target_file;
        } else {
            die("Error uploading payment screenshot.");
        }
    }

    // ✅ Start transaction
    $conn->begin_transaction();

    try {
        // ✅ Create the order after payment confirmation
        $order_status = "Pending";
        $order_sql = "INSERT INTO orders (email, total_price, order_status, recipient_name, phone_number, address, gcash_number, gcash_reference, payment_screenshot) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($order_sql);
$full_name = $user['first_name'] . ' ' . $user['last_name'];
// Adjust the bind_param to match the number of parameters and their types
$stmt->bind_param("sdsssssss", $email, $total_price, $order_status, $full_name, $user['contact_number'], $user['address'], $gcash_number, $gcash_reference, $screenshot_path);

$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();


        // ✅ Insert order items
        $insert_item_sql = "INSERT INTO order_items_backup (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
        foreach ($cart_items as $item) {
            $stmt = $conn->prepare($insert_item_sql);
            $stmt->bind_param("iisid", $order_id, $item['product_id'], $item['product_name'], $item['quantity'], $item['price']);
            $stmt->execute();
            $stmt->close();
        }

        // ✅ Clear cart after successful payment
        $clear_cart_sql = "DELETE FROM cart WHERE email = ?";
        $stmt = $conn->prepare($clear_cart_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();

        // ✅ Commit transaction
        $conn->commit();

        // Clear checkout session after order
        unset($_SESSION['checkout']);

        echo "Payment successful! Your order has been placed. <a href='user_orders.php'>View Orders</a>";
    } catch (Exception $e) {
        $conn->rollback();
        die("Error processing order: " . $e->getMessage());
    }
}

$conn->close();
?>

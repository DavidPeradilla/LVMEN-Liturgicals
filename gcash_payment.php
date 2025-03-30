<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['checkout'])) {
    $email = $_SESSION['checkout']['email'];
    $cart_items = $_SESSION['checkout']['cart_items'];
    $total_price = $_SESSION['checkout']['total_price'];

    $recipient_name = $_POST['recipient_name'];
    $phone_number = $_POST['phone_number'];
    $street = $_POST['street'];
    $unit_floor = $_POST['unit_floor'];
    $gcash_number = $_POST['gcash_number'];
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
        $order_sql = "INSERT INTO orders (email, total_price, order_status, recipient_name, phone_number, street, unit_floor, gcash_number, gcash_reference, payment_screenshot) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($order_sql);
        $stmt->bind_param("sdssssssss", $email, $total_price, $order_status, $recipient_name, $phone_number, $street, $unit_floor, $gcash_number, $gcash_reference, $screenshot_path);
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

        // ✅ Clear session data
        unset($_SESSION['checkout']);

        echo "Payment successful! Your order has been placed. <a href='user_orders.php'>View Orders</a>";
    } catch (Exception $e) {
        $conn->rollback();
        die("Error processing order: " . $e->getMessage());
    }
}

$conn->close();
?>

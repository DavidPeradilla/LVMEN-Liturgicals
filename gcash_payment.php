<?php
session_name("user_session"); // Add this line
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gcash_number = $_POST['gcash_number']; 
    $gcash_reference = $_POST['gcash_reference'];

    if (empty($gcash_number) || empty($gcash_reference)) {
        die("Invalid GCash payment details.");
    }

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

    $conn->begin_transaction();

    try {
        $order_status = "Pending";
        $order_sql = "INSERT INTO orders (email, total_price, order_status, recipient_name, phone_number, address, gcash_number, gcash_reference, payment_screenshot) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($order_sql);
        $full_name = $user['first_name'] . ' ' . $user['last_name'];
        $stmt->bind_param("sdsssssss", $email, $total_price, $order_status, $full_name, $user['contact_number'], $user['address'], $gcash_number, $gcash_reference, $screenshot_path);

        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        $insert_item_sql = "INSERT INTO order_items_backup (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
        foreach ($cart_items as $item) {
            $stmt = $conn->prepare($insert_item_sql);
            $stmt->bind_param("iisid", $order_id, $item['product_id'], $item['product_name'], $item['quantity'], $item['price']);
            $stmt->execute();
            $stmt->close();
        }

        $clear_cart_sql = "DELETE FROM cart WHERE email = ?";
        $stmt = $conn->prepare($clear_cart_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        unset($_SESSION['checkout']);
    } catch (Exception $e) {
        $conn->rollback();
        die("Error processing order: " . $e->getMessage());
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Payment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 600px;
        }

        h2 {
            color: (burlywood);
        }

        .form-group {
            margin-bottom: 20px;
        }

        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #2e8b57;
            color: white;
            padding: 12px 24px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        input[type="submit"]:hover {
            background-color: #245f3e;
        }

        a {
            color: #2e8b57;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .footer {
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }

        .footer a {
            color: #2e8b57;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Payment Confirmation</h2>

        <p>Your payment has been successfully processed. Thank you for your order!</p>

        <div class="footer">
            <p>Return to your <a href="profile.php">profile</a> to view your order details.</p>
        </div>
    </div>

</body>
</html>

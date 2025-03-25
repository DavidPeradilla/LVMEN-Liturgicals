<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email'])) {
    die("You need to log in.");
}

$email = $_SESSION['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient_name = $_POST['recipient_name'];
    $phone_number = $_POST['phone_number'];
    $street = $_POST['street'];
    $unit_floor = $_POST['unit_floor'];
    $gcash_number = $_POST['gcash_number'];
    $gcash_reference = $_POST['gcash_reference'];
    $order_id = $_POST['order_id'];
    $order_status = "Paid"; // Update order status to 'Paid'

    // ✅ Handle file upload
    $screenshot_path = null;
    if (isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] == 0) {
        $target_dir = "uploads/"; // Ensure this folder exists and is writable
        $file_name = time() . "_" . basename($_FILES["payment_screenshot"]["name"]);
        $target_file = $target_dir . $file_name;

        // Validate file type (only images)
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

    // ✅ Update order details in the database
    $sql = "UPDATE orders SET 
            recipient_name = ?, phone_number = ?, street = ?, unit_floor = ?, 
            gcash_number = ?, gcash_reference = ?, payment_screenshot = ?, order_status = ? 
            WHERE id = ? AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssds", $recipient_name, $phone_number, $street, $unit_floor, 
                      $gcash_number, $gcash_reference, $screenshot_path, $order_status, 
                      $order_id, $email);

    if ($stmt->execute()) {
        echo "Payment submitted successfully! <a href='user_orders.php'>View Orders</a>";
    } else {
        echo "Error updating order: " . $conn->error;
    }

    $stmt->close();
}
$conn->close();
?>

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

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Variable to store success message
$success_message = '';

// Handle form submission to update courier info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courier_name = $_POST['courier_name'] ?? '';
    $tracking_link = $_POST['tracking_link'] ?? '';

    $update_sql = "UPDATE orders SET courier_name = ?, tracking_link = ? WHERE id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("ssi", $courier_name, $tracking_link, $order_id);
    if ($stmt_update->execute()) {
        $success_message = "Courier information has been updated successfully!";
    }
    $stmt_update->close();
}

// Fetch order and user details
$sql = "SELECT 
            o.id, o.recipient_name, o.phone_number, o.total_price, 
            o.gcash_number, o.gcash_reference, o.payment_screenshot, 
            o.courier_name, o.tracking_link, o.address,
            u.first_name, u.last_name, u.contact_number
        FROM orders o
        LEFT JOIN users u ON o.id = u.id
        WHERE o.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

// Get order items
$order_items = [];
if ($order) {
    $stmt_items = $conn->prepare("SELECT product_name, quantity FROM order_items_backup WHERE order_id = ?");
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();
    while ($item = $result_items->fetch_assoc()) {
        $order_items[] = $item;
    }
    $stmt_items->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Tracking</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome CDN -->
    <style>
        body { font-family: Arial, sans-serif; 
                background-color: #f9f9f9; 
            margin: 20px; }

        .container { 
            width: 70%; 
            margin: auto; 
            background: white;
            padding: 20px; 
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            }
        h2, h3 { 
            text-align: center; 
        }

        table { width: 100%; 
            margin-top: 20px;
            border-collapse: collapse; 
        }

        th, td { 
            padding: 10px; 
            border: 1px solid #ddd; 
            text-align: center; }

        th { 
            background-color: #555; 
            color: white; }
        input[type="text"] { 
            width: 100%;
            padding: 8px; 
            margin-top: 5px; 
            border: 1px solid #ccc; 
            border-radius: 5px; }

        .btn-save, .btn-print, .btn-back { 
            padding: 10px 20px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            color: white; 
            display: flex;
            align-items: center;
        }
        .btn-save { 
            background-color: #007bff; }

        .btn-save:hover { 
            background-color: #0056b3; }

        .btn-print { 
            background-color: #28a745; margin-top: 1%; margin-left: 20%; margin-top:-4%; }

        .btn-print:hover { 
            background-color: #218838; }

        .btn-back { 
            background-color: rgb(61, 64, 62); }

        .btn-back:hover { 
            background-color: rgb(49, 50, 49); }

        .btn-container { 
            margin-top: 20px; 
        }

        .btn-container button i { margin-right: 8px; 
        } 
        
        .success-message { background-color: #28a745; 
            color: white; 
            padding: 10px; 
            margin-bottom: 20px; 
            text-align: center; 
            border-radius: 5px; }
      
    @media print {
        .btn-container {
            display: none;
        }
    }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success_message): ?>
            <div class="success-message">
                <p><?php echo $success_message; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!$order): ?>
            <h2>Order Not Found</h2>
        <?php else: ?>
            <h2>Order Details (ID: <?php echo $order_id; ?>)</h2>

            <p><strong>Recipient:</strong> <?php echo htmlspecialchars($order['recipient_name']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone_number']); ?></p>
            <p><strong>Total Price:</strong> ₱<?php echo number_format($order['total_price'], 2); ?></p>
            <p><strong>GCash Number:</strong> <?php echo htmlspecialchars($order['gcash_number']); ?></p>
            <p><strong>GCash Reference:</strong> <?php echo htmlspecialchars($order['gcash_reference']); ?></p>

            <h3>Products Ordered</h3>
            <table>
                <tr><th>Product Name</th><th>Quantity</th></tr>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <form method="post">
    <label for="courier_name">Courier Name</label>
    <input type="text" name="courier_name" id="courier_name" value="<?php echo htmlspecialchars($order['courier_name'] ?? ''); ?>">

    <label for="tracking_link">Tracking Link</label>
    <input type="text" name="tracking_link" id="tracking_link" value="<?php echo htmlspecialchars($order['tracking_link'] ?? ''); ?>">

    <div class="btn-container">
        <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Save Courier Info</button>
        <button type="button" class="btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
    </div>
</form>

<div class="btn-container">
    <button class="btn btn-back" onclick="window.history.back()"><i class="fas fa-arrow-left"></i> Back</button>
</div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>

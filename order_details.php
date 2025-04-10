<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Handle form submission to update courier info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courier_name = $_POST['courier_name'] ?? '';
    $tracking_link = $_POST['tracking_link'] ?? '';

    $update_sql = "UPDATE orders SET courier_name = ?, tracking_link = ? WHERE id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("ssi", $courier_name, $tracking_link, $order_id);
    $stmt_update->execute();
    $stmt_update->close();
}


$sql = "SELECT 
            o.id, o.recipient_name, o.phone_number, 
            o.street, o.total_price, 
            o.gcash_number, o.gcash_reference, o.payment_screenshot, 
            o.courier_name, o.tracking_link,
            u.first_name, u.last_name, u.address, u.contact_number
        FROM orders o
        LEFT JOIN users u ON o.recipient_name = CONCAT(u.first_name, ' ', u.last_name) 
        WHERE o.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();


$order_items_sql = "SELECT oi.product_name, oi.quantity
                    FROM order_items_backup oi
                    WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($order_items_sql);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$order_items = [];
while ($item = $result_items->fetch_assoc()) {
    $order_items[] = $item;
}

$stmt->close();
$stmt_items->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="sidebar2.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 20px; }
        h2 { text-align: center; }
        .container { width: 70%; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        img { width: 50px; border-radius: 5px; }

        .btn-container { text-align: center; margin-top: 20px; }
        .btn { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 5px; }
        .btn-back { background: #6c757d; color: white; }
        .btn-back:hover { background: #5a6268; }
        .btn-print { background: #28a745; color: white; }
        .btn-print:hover { background: #218838; }
        .btn-save { background: #007bff; color: white; }
        .btn-save:hover { background: #0056b3; }

        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }

        @media print {
            .btn-container, form { display: none; }
            body { background: white; }
            .container { box-shadow: none; width: 100%; padding: 10px; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Order Details (Order ID: <?php echo $order_id; ?>)</h2>

    <p><strong>Recipient:</strong> <?php echo htmlspecialchars($order['recipient_name']); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone_number']); ?></p>
    <p><strong>Total Price:</strong> ₱<?php echo number_format($order['total_price'], 2); ?></p>
    <p><strong>GCash Number:</strong> <?php echo htmlspecialchars($order['gcash_number']); ?></p>
    <p><strong>GCash Reference:</strong> <?php echo htmlspecialchars($order['gcash_reference']); ?></p>

    <h3>Ordered Products</h3>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
        </tr>
        <?php foreach ($order_items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
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
            <button class="btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
        </div>
    </form>


    <div class="btn-container">
        <button class="btn btn-back" onclick="window.history.back()"><i class="fas fa-arrow-left"></i> Back</button>
    </div>

</div>

</body>
</html>

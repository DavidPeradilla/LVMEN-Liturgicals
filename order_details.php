<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

$sql = "SELECT 
            o.id, o.recipient_name, o.phone_number, 
            o.street, o.total_price, 
            o.gcash_number, o.gcash_reference, o.payment_screenshot, 
            u.first_name, u.last_name, u.address, u.contact_number,
            oi.product_name, oi.quantity
        FROM orders o
        LEFT JOIN order_items_backup oi ON o.id = oi.order_id
        LEFT JOIN users u ON o.recipient_name = CONCAT(u.first_name, ' ', u.last_name) 
        WHERE o.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Order Details</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            
            /* Style for buttons */
            .btn-container { text-align: center; margin-top: 20px; }
            .btn { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 5px; }
            .btn-back { background: #6c757d; color: white; }
            .btn-back:hover { background: #5a6268; }
            .btn-print { background: #28a745; color: white; }
            .btn-print:hover { background: #218838; }
            
            /* Hide print button when printing */
            @media print {
                .btn-container { display: none; }
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
            <?php
            do { ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                </tr>
            <?php } while ($order = $result->fetch_assoc()); ?>
        </table>

        <!-- Print & Back Buttons -->
        <div class="btn-container">
            <a href="admin_orders.php"><button class="btn btn-back"><i class="fas fa-arrow-left"></i> Back to Orders</button></a>
            <button class="btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
        </div>
    </div>
    </body>
    </html>


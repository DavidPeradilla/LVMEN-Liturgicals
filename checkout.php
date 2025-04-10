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

// Fetch cart items
$sql = "SELECT cart.product_id, products.name AS product_name, products.price, cart.quantity, (products.price * cart.quantity) AS total_price 
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

$_SESSION['checkout'] = [
    'email' => $email,
    'cart_items' => $cart_items,
    'total_price' => $total_order_price
];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 60%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[readonly] {
            background-color: #f3f3f3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: #fff;
            border-radius: 5px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background: #f4f4f4;
            font-weight: bold;
        }

        .file-upload {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .file-upload img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            display: none;
        }

        .total-section {
            text-align: right;
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
        }

        .checkout-btn {
            background: #ff5722;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            text-align: center;
        }

        .checkout-btn:hover {
            background: #e64a19;
        }

        .cancel-btn {
            background: #bbb;
            margin-top: 10px;
        }

        .cancel-btn:hover {
            background: #999;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Checkout</h2>

    <form action="gcash_payment.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
    <label>Email Address:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly required>

    <label>Recipient's Name:</label>
    <input type="text" name="recipient_name" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" readonly required>

    <label>Shipping Address:</label>
    <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" readonly required>

    <label>Phone Number:</label>
    <input type="text" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number']); ?>" readonly required>




        <h3>Order Summary</h3>
        <table>
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
    <input type="text" value="GCash: +123-456-7890" disabled>
    <input type="hidden" name="payment_method" value="GCash">

    <label>GCash Number:</label>
    <input type="text" name="gcash_number" required> 

    <label>GCash Reference Number:</label>
    <input type="text" name="gcash_reference" required>

    <label>Upload Payment Screenshot:</label>
    <div class="file-upload">
        <input type="file" name="payment_screenshot" accept="image/*" required onchange="previewImage(event)">
        <img id="preview" alt="Payment Screenshot">
    </div>

    <div class="total-section">
        Total Price: ₱<?php echo number_format($total_order_price, 2); ?>
    </div>

    <input type="hidden" name="total_price" value="<?php echo $total_order_price; ?>">

    <button type="submit" class="checkout-btn">Confirm Payment</button>
    <button type="button" class="checkout-btn cancel-btn" onclick="cancelOrder()">Cancel</button>
</form>
</div>

<script>
    function previewImage(event) {
        const image = document.getElementById("preview");
        image.src = URL.createObjectURL(event.target.files[0]);
        image.style.display = "block";
    }

    function cancelOrder() {
        if (confirm("Are you sure you want to cancel the order?")) {
            window.location.href = "user_products.php"; // Redirect back to products page
        }
    }

    function confirmSubmission() {
    return confirm("Are you sure you want to confirm the payment?");
}


function validateForm() {
    // Get the values of the shipping address and phone number fields
    const address = document.querySelector('input[name="address"]').value;
    const phoneNumber = document.querySelector('input[name="contact_number"]').value;

    // Check if the address or phone number is empty
    if (!address.trim() || !phoneNumber.trim()) {
        alert("You need to add your shipping address and phone number before confirming the payment.");
        
        // Redirect to profile.php after the alert
        window.location.href = "profile.php"; // Redirect to the profile page
        return false; // Prevent form submission
    }

    // If both fields are filled, proceed with the confirmation
    return confirm("Are you sure you want to confirm the payment?");
}

function cancelOrder() {
    if (confirm("Are you sure you want to cancel the order?")) {
        window.location.href = "user_products.php"; // Redirect back to products page
    }
}
</script>

</body>
</html>

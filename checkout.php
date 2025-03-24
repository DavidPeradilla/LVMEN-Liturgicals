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
    <script src="https://cdn.tailwindcss.com"></script>

    
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

<div class="max-w-6xl mx-auto p-6 bg-white shadow-lg rounded-lg mt-10">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Checkout</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Checkout Form (Left) -->
    <form action="gcash_payment.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()" class="space-y-4">
        <div>
            <label class="block font-medium text-gray-700">Email Address:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email); ?>" readonly required class="w-full mt-1 p-2 border rounded-md bg-gray-100">
        </div>

        <div>
            <label class="block font-medium text-gray-700">Recipient's Name:</label>
            <input type="text" name="recipient_name" value="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" readonly required class="w-full mt-1 p-2 border rounded-md bg-gray-100">
        </div>

        <div>
            <label class="block font-medium text-gray-700">Shipping Address:</label>
            <input type="text" name="address" 
    placeholder="Enter Address" 
    value="<?= htmlspecialchars($user['address']); ?>" 
    <?= empty($user['address']) ? '' : 'readonly'; ?> 
    required class="w-full mt-1 p-2 border rounded-md <?= empty($user['address']) ? '' : 'bg-gray-100'; ?>">

        </div>

        <div>
            <label class="block font-medium text-gray-700">Phone Number:</label>
            <input type="text" name="contact_number" 
    placeholder="Enter Phone Number" 
    value="<?= htmlspecialchars($user['contact_number']); ?>" 
    <?= empty($user['contact_number']) ? '' : 'readonly'; ?> 
    required class="w-full mt-1 p-2 border rounded-md <?= empty($user['contact_number']) ? '' : 'bg-gray-100'; ?>">

        </div>

        <div>
            <label class="block font-medium text-gray-700">GCash Number:</label>
            <input type="text" name="gcash_number" placeholder="Insert your GCash Number (e.g., 0912-345-6789)" required class="w-full mt-1 p-2 border rounded-md" maxlength="13" pattern="\d{4}-\d{3}-\d{4}" title="GCash number must be in the format: 0912-345-6789">
        </div>

        <div>
            <label class="block font-medium text-gray-700">GCash Reference Number:</label>
            <input type="text" name="gcash_reference" placeholder="Insert Reference Number" required class="w-full mt-1 p-2 border rounded-md" maxlength="16" pattern="\d{16}" title="Reference number must be exactly 16 numeric characters">
        </div>

        <div>
            <label class="block font-medium text-gray-700">Upload Payment Screenshot:</label>
            <div class="flex items-center gap-4 mt-2">
                <input type="file" name="payment_screenshot" accept="image/*" required onchange="previewImage(event)" class="w-full">
                <img id="preview" class="w-24 h-24 object-cover rounded hidden" alt="Screenshot Preview">
            </div>
        </div>

        <input type="hidden" name="payment_method" value="GCash">
        <input type="hidden" name="total_price" value="<?= $total_order_price; ?>">

        <div class="flex gap-4 mt-4">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-md">Confirm Payment</button>
            <button type="button" onclick="cancelOrder()" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
        </div>
    </form>

    <!-- Order Summary (Right) -->
    <div>
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Order Summary</h3>
        <table class="w-full text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left p-2 border">Product Name</th>
                    <th class="text-left p-2 border">Price</th>
                    <th class="text-left p-2 border">Qty</th>
                    <th class="text-left p-2 border">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr class="border-t">
                        <td class="p-2 border"><?= htmlspecialchars($item['product_name']); ?></td>
                        <td class="p-2 border">₱<?= number_format($item['price'], 2); ?></td>
                        <td class="p-2 border"><?= $item['quantity']; ?></td>
                        <td class="p-2 border">₱<?= number_format($item['total_price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-right mt-4 text-lg font-bold">
            Total: ₱<?= number_format($total_order_price, 2); ?>
        </div>

        <div class="mt-6 bg-blue-100 text-blue-800 p-3 rounded-md">
            <strong>Payment Method:</strong> GCash: +123-456-7890
        </div>
    </div>
</div>



<script>
    function previewImage(event) {
        const image = document.getElementById("preview");
        image.src = URL.createObjectURL(event.target.files[0]);
        image.style.display = "block";
    }

    function cancelOrder() {
        if (confirm("Are you sure you want to cancel the order?")) {
            window.location.href = "user_products.php"; 
        }
    }

    function confirmSubmission() {
    return confirm("Are you sure you want to confirm the payment?");
}


function validateForm() {
    
    const address = document.querySelector('input[name="address"]').value;
    const phoneNumber = document.querySelector('input[name="contact_number"]').value;

   
    if (!address.trim() || !phoneNumber.trim()) {
        alert("You need to add your shipping address and phone number before confirming the payment.");
        
       
        window.location.href = "profile.php"; 
        return false; 
    }

    
    return confirm("Are you sure you want to confirm the payment?");
}

function cancelOrder() {
    if (confirm("Are you sure you want to cancel the order?")) {
        window.location.href = "user_products.php"; 
    }
}
</script>

</body>
</html>

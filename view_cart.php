<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email'])) {
    die("You need to log in to view your cart.");
}

$email = $_SESSION['email'];

// Fetch cart items with product details
$sql = "SELECT cart.id, products.name, products.price, cart.quantity, products.image 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            border-radius: 5px;
            object-fit: cover;
        }

        .cart-details {
            flex-grow: 1;
            margin-left: 15px;
        }

        .cart-details h3 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .cart-details p {
            margin: 5px 0;
            font-size: 14px;
            color: #777;
        }

        .quantity {
            display: flex;
            align-items: center;
        }

        .quantity button {
            border: none;
            background: #ff5722;
            color: white;
            font-size: 16px;
            width: 30px;
            height: 30px;
            cursor: pointer;
            border-radius: 5px;
        }

        .quantity input {
            width: 40px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin: 0 5px;
        }

        .remove-btn {
            background: #ff4444;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .checkout-section {
            text-align: right;
            margin-top: 20px;
        }

        .checkout-section p {
            font-size: 18px;
            font-weight: bold;
        }

        .checkout-btn {
            background: #ff5722;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .checkout-btn:hover {
            background: #e64a19;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Your Shopping Cart</h2>

    <?php while ($row = $result->fetch_assoc()) { ?>
    <div class="cart-item">
        <img src="<?php echo $row['image']; ?>" alt="Product">
        <div class="cart-details">
            <h3><?php echo $row['name']; ?></h3>
            <p>₱<?php echo number_format($row['price'], 2); ?></p>
        </div>

        <div class="quantity">
            <button onclick="decreaseQuantity(<?php echo $row['id']; ?>)">-</button>
            <input type="text" id="quantity-<?php echo $row['id']; ?>" value="<?php echo $row['quantity']; ?>" readonly>
            <button onclick="increaseQuantity(<?php echo $row['id']; ?>)">+</button>
        </div>

        <form action="remove_from_cart.php" method="POST">
            <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
            <button type="submit" class="remove-btn"><i class="fas fa-trash"></i></button>
        </form>
    </div>
    <?php } ?>

    <div class="checkout-section">
        <p>Total: ₱ <span id="total-price">0.00</span></p>
        <a href="checkout.php"><button class="checkout-btn">Proceed to Checkout</button></a>
    </div>
</div>

<script>
    function increaseQuantity(id) {
        let quantityInput = document.getElementById("quantity-" + id);
        let quantity = parseInt(quantityInput.value);
        quantityInput.value = quantity + 1;
        updateTotal();
    }

    function decreaseQuantity(id) {
        let quantityInput = document.getElementById("quantity-" + id);
        let quantity = parseInt(quantityInput.value);
        if (quantity > 1) {
            quantityInput.value = quantity - 1;
            updateTotal();
        }
    }

    function updateTotal() {
        let total = 0;
        let cartItems = document.querySelectorAll(".cart-item");
        cartItems.forEach(item => {
            let price = parseFloat(item.querySelector(".cart-details p").innerText.replace("₱", ""));
            let quantity = parseInt(item.querySelector("input").value);
            total += price * quantity;
        });
        document.getElementById("total-price").innerText = total.toFixed(2);
    }

    window.onload = updateTotal;
</script>

</body>
</html>


<?php $conn->close(); 
?>

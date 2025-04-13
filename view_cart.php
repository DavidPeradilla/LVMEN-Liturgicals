<?php
session_name("user_session"); // Only if you used this in your login/logout files
session_start();


$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$cartEmpty = true; 

if (!isset($_SESSION['email'])) {
    echo "Please log in to view your cart.";
    exit;
}

// Get the user's email
$email = $_SESSION['email'];

// Fetch cart items for the logged-in user
$sql = "SELECT cart.id, cart.quantity, products.name, products.price, products.image 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.email = ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartEmpty = ($result->num_rows === 0);
    $stmt->close();
} else {
    die("Query failed: " . $conn->error);
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="LVMEN.css"> 
    <link rel="stylesheet" type="text/css" href="navbar2.css"> 
    <style>
       body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
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

<header> 
    <a href="LVMEN.php"> <img class="logo" src="Img/LVMEN Logo.jpg" style="margin-left: 1%;" width="80px" height="70px"></a>
    <nav class="navbar"> 
        <ul class="nav-links">
            <a href="LVMEN.php"> <li> HOMEPAGE </li> </a>  
            <a href="AboutUs.php"> <li> ABOUT US  </li> </a>
            <a href="user_products.php"> <li> CATALOG </li> </a>
            <a href="Contact.php"> <li> CONTACT US </li> </a>
            <a href="FAQs.php"> <li> FAQs </li> </a>
            <a href="profile.php"> PROFILE </a>

            <?php if (isset($_SESSION['email'])): ?>
                <a href="logout.php" class="login-btn"> <li> LOGOUT </li> </a>
            <?php else: ?>
                <a href="login.php" class="login-btn"> <li> LOGIN </li> </a>
            <?php endif; ?>
            <a href="view_cart.php" class="cart-link">🛒</a>
        </ul>
    </nav> 
</header>

<br> <br> <br> <br> <br> <br>

<div class="container">
    <h2>Your Shopping Cart</h2>

    <?php if ($cartEmpty): ?>
        <p align="center">Your cart is empty.</p>
    <?php else: ?>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="cart-item" data-id="<?php echo $row['id']; ?>">
                <img src="<?php echo $row['image']; ?>" alt="Product">
                <div class="cart-details">
                    <h3><?php echo $row['name']; ?></h3>
                    <p>₱<span class="price"><?php echo number_format($row['price'], 2); ?></span></p>
                </div>

                <div class="quantity">
                    <button onclick="updateQuantity(<?php echo $row['id']; ?>, -1)">-</button>
                    <input type="text" id="quantity-<?php echo $row['id']; ?>" value="<?php echo $row['quantity']; ?>" readonly>
                    <button onclick="updateQuantity(<?php echo $row['id']; ?>, 1)">+</button>
                </div>

                <form action="remove_from_cart.php" method="POST">
                    <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="remove-btn"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        <?php } ?>

        <div class="checkout-section">
            <?php
            $total = 0;
            mysqli_data_seek($result, 0); // Reset result pointer
            while ($row = $result->fetch_assoc()) {
                $total += $row['price'] * $row['quantity'];
            }
            ?>
            <p>Total: ₱ <span id="total-price"><?php echo number_format($total, 2); ?></span></p>
            <a href="checkout.php"><button class="checkout-btn">Proceed to Checkout</button></a>
        </div>
    <?php endif; ?>
</div>

<script>
    function updateQuantity(id, change) {
        let quantityInput = document.getElementById("quantity-" + id);
        let newQuantity = parseInt(quantityInput.value) + change;
        
        if (newQuantity < 1) return;

        fetch("update_cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `cart_id=${id}&quantity=${newQuantity}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                quantityInput.value = newQuantity;
                updateTotal();
            } else {
                alert("Error updating quantity.");
            }
        })
        .catch(error => console.error("Error:", error));
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll(".cart-item").forEach(item => {
            let price = parseFloat(item.querySelector(".price").innerText.replace(",", ""));
            let quantity = parseInt(item.querySelector("input").value);
            total += price * quantity;
        });
        document.getElementById("total-price").innerText = total.toLocaleString(undefined, { minimumFractionDigits: 2 });
    }

    window.onload = updateTotal;
</script>

</body>
</html>

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
    <link rel="stylesheet" type="text/css" href="navbar3.css"> 
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

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
  <a href="LVMEN.php">
    <img class="logo" src="Img/LVMEN Logo.jpg" style="margin-left: 1%;" width="80px" height="70px">
  </a>

  <nav class="navbar"> 
    <ul class="nav-links">
      <li><a href="LVMEN.php"> HOMEPAGE </a></li>
      <li><a href="AboutUs.php"> ABOUT US </a></li>
      <li><a href="user_products.php"> CATALOG </a></li>
      <li><a href="Contact.php"> CONTACT US </a></li>
      <li><a href="FAQs.php"> FAQs </a></li>
      <li><a href="profile.php"><i class="fas fa-user"></i></a></li>
      <li>
        <a href="view_cart.php" class="cart-link">
          <i class="fas fa-shopping-cart"></i>
        </a>
      </li>

      <?php if (isset($_SESSION['email'])): ?>
        <li class="right-align"><a href="logout.php" class="login-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a></li>
      <?php else: ?>
        <li class="right-align"><a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> LOGIN</a></li>
      <?php endif; ?>
    </ul>
  </nav> 
</header>

<br> <br> <br>
      <!--CART-->
      
      <div class="container mx-auto max-w-4xl p-6 bg-white shadow-lg rounded-2xl my-10">
    <h2 class="text-2xl font-semibold text-center mb-6">Your Shopping Cart</h2>

    <?php if ($cartEmpty): ?>
        <p class="text-center text-gray-500">Your cart is empty.</p>
    <?php else: ?>
        <div class="space-y-4">
            <?php while ($row = $result->fetch_assoc()) { ?>
              <div class="cart-item flex items-center justify-between bg-gray-50 p-4 rounded-xl shadow-sm min-h-[80px]">
                    <!-- Image + Details -->
                    <div class="flex items-center gap-4">
                        <img src="<?php echo $row['image']; ?>" alt="Product" class="w-16 h-16 rounded-md object-cover border border-gray-200">
                        <div>
                        <h3 class="text-base font-medium text-gray-800  max-w-[150px]">
    <?php echo $row['name']; ?>
</h3>

                            <p class="text-gray-600 text-sm">₱<span class="price"><?php echo number_format($row['price'], 2); ?></span></p>
                        </div>
                    </div>

                    <!-- Quantity Controls -->
                    <div class="flex items-center justify-center space-x-0 h-10">
    <button
        onclick="updateQuantity(<?php echo $row['id']; ?>, -1)"
        class="w-10 h-10 flex items-center justify-center bg-gray-200 text-gray-700 rounded-l hover:bg-gray-300"
    >-</button>

    <input
        type="text"
        id="quantity-<?php echo $row['id']; ?>"
        value="<?php echo $row['quantity']; ?>"
        readonly
        class="w-12 h-10 text-center border-y border-gray-300 text-sm appearance-none"
    />

    <button
        onclick="updateQuantity(<?php echo $row['id']; ?>, 1)"
        class="w-10 h-10 flex items-center justify-center bg-gray-200 text-gray-700 rounded-r hover:bg-gray-300"
    >+</button>
</div>


                    <!-- Remove Button -->
                    <form action="remove_from_cart.php" method="POST" class="ml-3">
                        <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="text-red-500 hover:text-red-700 text-lg">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            <?php } ?>
        </div>

        <!-- Checkout Section -->
        <div class="checkout-section mt-8 bg-gray-100 p-6 rounded-xl text-center shadow-inner">
            <?php
            $total = 0;
            mysqli_data_seek($result, 0);
            while ($row = $result->fetch_assoc()) {
                $total += $row['price'] * $row['quantity'];
            }
            ?>
            <p class="text-xl font-semibold mb-4">Total: ₱ <span id="total-price"><?php echo number_format($total, 2); ?></span></p>
            <a href="checkout.php">
                <button class="checkout-btn bg-green-500 hover:bg-green-600 text-white font-medium px-6 py-2 rounded-lg transition duration-200">
                    Proceed to Checkout
                </button>
            </a>
        </div>
    <?php endif; ?>
</div>




        <!--SCRIPT-->
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

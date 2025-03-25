<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories for filter dropdown
$categoryResult = $conn->query("SELECT * FROM categories"); 
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$whereClause = $categoryFilter ? "WHERE products.category_id = '$categoryFilter'" : "";

// Fetch products with category names
$sql = "SELECT products.id, products.name, products.price, products.quantity, products.image, categories.category_name 
        FROM products 
        JOIN categories ON products.category_id = categories.id 
        $whereClause
        ORDER BY products.id DESC";
$result = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog</title>
    <link rel="stylesheet" type="text/css" href="LVMEN.css">
    <link rel="stylesheet" type="text/css" href="navbar.css"> 
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        
        .container {
            width: 90%;
            margin: auto;
            padding-top: 20px;
            margin-top: 7%;
        }

        /* Filter Dropdown */
        .filter-container {
            margin-top: 20px;
            text-align: center;
        }
        
        .filter-container select {
            padding: 10px;
            font-size: 16px;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            justify-content: center;
        }

        /* Product Card */
        .product-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            max-width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-card h3 {
            font-size: 18px;
            margin: 10px 0;
        }

        .product-card p {
            color: #666;
            font-size: 16px;
        }

        .product-card .price {
            font-weight: bold;
            color: #ff5733;
            margin-top: 5px;
        }

        .product-card form {
            margin-top: 10px;
        }

        .product-card input[type="number"] {
            width: 50px;
            padding: 5px;
            text-align: center;
        }

        .product-card button {
            background-color: #ff5733;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .product-card button:hover {
            background-color: #d84315;
        }

        .cart-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<header> 
<a href="LVMEN.php"> <img class="logo" src="Img/LVMEN Logo.jpg" style="margin-left: 1%;" width="80px" height="70px"></a>
  <nav class="navbar"> 
     <ul class="nav-links">
     <a href="LVMEN.php"> <li> HOMEPAGE </li> </a>  
      <a href="AboutUs.php"> <li> ABOUT US  </li> </a>
      <a href="user_products.php"> <li> CATALOG </li> </a>
      <a href="Contact.php"> <li> CONTACT US </li> </a>
      <a href="FAQs.php"> <li> FAQs </li> </a>
      <a href="profile.php"> Profile </a>


      <?php if (isset($_SESSION['email'])): ?>
      <a href="logout.php" class="login-btn"> <li> LOGOUT </li> </a>
      <?php else: ?>
      <a href="login.php" class="login-btn"> <li> LOGIN </li> </a>
      <?php endif; ?>
     </ul>
  </nav> 
</header>
<!-- END -->

<div class="container">

    <h2 style="text-align:center;">Available Products</h2>

    <!-- Filter Dropdown -->
   <div class="filter-container">
    <form method="GET" action="">
        <label for="category">Filter by Category:</label>
        <select name="category" id="category" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php while ($category = $categoryResult->fetch_assoc()) { ?>
                <option value="<?php echo $category['id']; ?>" <?php if ($categoryFilter == $category['id']) echo 'selected'; ?>>
                    <?php echo $category['category_name']; ?>
                </option>
            <?php } ?>
        </select>
    </form>
</div>

    <!-- Product Grid -->
    <div class="product-grid">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="product-card">
                <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                <h3><?php echo $row['name']; ?></h3>
                <p class="price">₱<?php echo number_format($row['price'], 2); ?></p>
                <p>Available: <?php echo $row['quantity']; ?></p>

                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $row['quantity']; ?>" required>
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        <?php } ?>
    </div>

    <a href="view_cart.php" class="cart-link">🛒 View Cart</a>
    <a href="LVMEN.php" class="cart-link">🏠 Back to Home</a>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let messageBox = document.getElementById("message-box");
        if (messageBox) {
            messageBox.style.display = "block";
            setTimeout(function() {
                messageBox.style.display = "none";
            }, 3000);
        }
    });
</script>

</body>
</html>

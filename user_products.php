<?php
session_name("user_session"); 
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$categoryResult = $conn->query("SELECT * FROM categories"); 
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$whereClause = $categoryFilter ? "WHERE products.category_id = '$categoryFilter'" : "";


$products_per_page = 15; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $products_per_page;


$count_sql = "SELECT COUNT(*) as total FROM products $whereClause";
$count_result = $conn->query($count_sql);
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $products_per_page);


$sql = "SELECT products.id, products.name, products.price, products.image, products.description, categories.category_name 
        FROM products 
        JOIN categories ON products.category_id = categories.id 
        $whereClause
        ORDER BY RAND()
        LIMIT $products_per_page OFFSET $offset";



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
    <link rel="stylesheet" type="text/css" href="footer3.css"> 
    <link rel="stylesheet" type="text/css" href="navbar3.css"> 
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background:rgb(243, 239, 239);
        }

    
    .container {
        display: flex;
        gap: 25px;
        max-width: 1200px;
        margin: 50px auto;
        padding: 20px;
    }

    
    .filter-sidebar {
        width: 300px;
        background: rgba(255, 255, 255, 0.7); 
        padding: 25px;
        border-radius: 15px;
        box-shadow: 5px 10px 20px rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(10px); 
        height: 50%;
    }

    .filter-sidebar:hover {
        transform: translateY(-3px);
        box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.2);
    }

    
    .filter-title {
        font-size: 22px;
        font-weight: bold;
        color: #333;
        text-align: center;
        padding-bottom: 10px;
        border-bottom: 2px solid #ddd;
        margin-bottom: 15px;
        letter-spacing: 1px;
    }

    
    .filter-group {
        margin-top: 15px;
    }

    
    .filter-label {
        font-size: 16px;
        font-weight: 600;
        color: #555;
        display: block;
        margin-bottom: 8px;
    }

    
    .filter-select {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border-color:rgb(102, 105, 109);
        border-radius: 10px;
        background: #fff;
        color: #333;    
        cursor: pointer;
        background-position: right 12px center;
        background-size: 16px;
    }

    .filter-select:hover, .filter-select:focus {
        background: #eef5ff;
        border-color:rgb(102, 105, 109);
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.15);
    }

    
    .content {
        flex: 1;
        background: #ffffff;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 5px 10px 20px rgba(0, 0, 0, 0.1);
    }

    
    .section-title {
        font-size: 26px;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
        letter-spacing: 1px;
    }

    
    @media (max-width: 1024px) {
        .container {
            flex-direction: column;
            align-items: center;
        }
        .filter-sidebar {
            width: 90%;
        }
    }

    
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        justify-content: center;
    }

    
    .product-card {
        background: white;
        border-radius: 10px;
        box-shadow: 5px 4px 8px 5px rgba(0, 0, 0, 0.1);
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
        cursor: pointer; 
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

    /* MODAL STYLING */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        max-width: 90vw;
        max-height: 90vh;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 10px;
    }

    .close {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 30px;
        color: white;
        cursor: pointer;
    }
    

    </style>
</head>
<body>

<!-- NAVBAR -->
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

      
      <li><a href="profile.php"><i class="fas fa-user"></i> </a></li>

      
      <li><a href="view_cart.php" class="cart-link">
        <i class="fas fa-shopping-cart"></i>
      </a></li>

      
      <?php if (!isset($_SESSION['email'])): ?>
        <li><a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> LOGIN</a></li>
      <?php endif; ?>
      
    </ul>
  </nav>
</header>
<!-- END -->

<br> <br> <br>
<div class="container">
    
    <div class="filter-sidebar">
        <h2 class="filter-title">Filter Products</h2>
        <form method="GET" action="">
            <label for="category" class="filter-label">Category:</label>
            <select name="category" id="category" class="filter-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php while ($category = $categoryResult->fetch_assoc()) { ?>
                    <option value="<?php echo $category['id']; ?>" <?php if ($categoryFilter == $category['id']) echo 'selected'; ?>>
                        <?php echo $category['category_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </form>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2 class="section-title">Available Products</h2>
        <div class="product-grid">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="product-card">
                    <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" onclick="openModal('<?php echo $row['image']; ?>')">
                    <h3><?php echo $row['name']; ?></h3>
                    <p class="price">₱<?php echo number_format($row['price'], 2); ?></p>
                    <button onclick="showDescription(`<?php echo addslashes($row['name']); ?>`, `<?php echo addslashes($row['description']); ?>`)">More</button>
                    <form onsubmit="addToCart(event, <?php echo $row['id']; ?>, this)">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" max="10" required>
                        <button type="submit">Add to Cart</button>
                    </form>
                    <span id="status-<?php echo $row['id']; ?>" class="status-message"></span>
                </div>
            <?php } ?>
        </div>


        <div style="text-align: center; margin-top: 30px;">
    <?php if ($total_pages > 1): ?>
        <div style="display: inline-block; padding: 10px;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a 
                    href="?page=<?php echo $i; ?><?php if ($categoryFilter) echo '&category=' . $categoryFilter; ?>" 
                    style="
                        display: inline-block;
                        margin: 0 5px;
                        padding: 8px 12px;
                        background-color: <?php echo ($i == $page) ? '#ff5733' : '#eee'; ?>;
                        color: <?php echo ($i == $page) ? '#fff' : '#333'; ?>;
                        text-decoration: none;
                        border-radius: 5px;
                        font-weight: bold;
                    ">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>




    </div>
</div>
<!-- END -->




<!-- MODAL -->

<div id="descModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeDescModal()">&times;</span>
        <h3 id="modalTitle"></h3>
        <p id="modalDesc"></p>
    </div>
</div>

<script>
function showDescription(title, description) {
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalDesc').innerText = description;
    document.getElementById('descModal').style.display = "flex";
}

function closeDescModal() {
    document.getElementById('descModal').style.display = "none";
}
</script>

<div id="imageModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img id="modalImage" class="modal-content">
</div>

<script>
    function openModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModal').style.display = "flex";
    }

    function closeModal() {
        document.getElementById('imageModal').style.display = "none";
    }
</script>

    <script>
    function addToCart(event, productId, form) {
        event.preventDefault(); 

        <?php if (!isset($_SESSION['email'])) { ?>
            alert("Please log in to add items to your cart.");
            window.location.href = "login.php";
            return;
        <?php } ?>

        let formData = new FormData(form);

        fetch("add_to_cart.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log("Server Response:", data); 

            let statusElement = document.getElementById('status-' + productId);
            if (data.trim() === "success") {
                statusElement.innerText = "Added to cart!";
                statusElement.style.color = "green";
            } else {
                statusElement.innerText = "Failed to add: " + data;
                statusElement.style.color = "red";
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
        });
    }

    </script>

<!--FOOTER-->
<footer>
   <div class="container">
    <div class="get-in-touch">
              <h4>Get in Touch</h4>
              <a href="https://www.facebook.com/LvmenLiturgicalVestments" target="_blank">
                <img src="Img/facebook.png" alt="Facebook">
              </a>
              <a href="#" target="_blank">
                <img src="Img/twitter.png" alt="Twitter">
              </a>
              <a href="https://www.instagram.com/explore/locations/108212715189138/dankatsu/" target="_blank">
                <img src="Img/instagram.png" alt="Instagram">
              </a>
           </div>
   </div>
   
   <div class="footer-bottom">
       <p>&copy; 2025 LVMEN Liturgicals. All Rights Reserved.</p>
       <p><a href="Contact.php">Contact Us</a> | <a href="/privacy-policy">Terms and Condition</a></p>
   </div>
</footer>
 <!--END--> 

</body>
</html>
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

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM featured_products WHERE id = $id");
    header("Location: admin_featured.php");
    exit;
}

// Handle adding featured product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_featured'])) {
    if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);
        
        // Check if the product is already in the featured products list
        $check = $conn->query("SELECT * FROM featured_products WHERE id = $productId");
        if ($check->num_rows === 0) {
            $insert = $conn->query("INSERT INTO featured_products (id) VALUES ($productId)");
            if ($insert) {
                header("Location: admin_featured.php?success=added");
                exit;
            } else {
                header("Location: admin_featured.php?error=insert_failed");
                exit;
            }
        }
    }        
}

// Fetch all products
$productResult = $conn->query("SELECT * FROM products");

// Fetch all featured products
$result = $conn->query("SELECT * FROM featured_products");

// Get product count
$countResult = $conn->query("SELECT COUNT(*) as total FROM featured_products");
$countRow = $countResult->fetch_assoc();
$totalProducts = $countRow['total'];


// Fetch all featured products with additional product details (including category name)
$query = "
SELECT p.id, p.name, p.price, p.image, p.description, c.category_name
FROM featured_products fp
JOIN products p ON fp.id = p.id
JOIN categories c ON p.category_id = c.id

";

$result = $conn->query($query);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Featured Products</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .main-content {
            margin-left: 78px;
            padding: 20px;
            background-color: #ecf0f1;
            height: 230vh;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">




<!-- Navbar -->
<div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="admin_sales.php"><i class="fas fa-tachometer-alt"></i><span>Overview</span></a>
        <a href="upload.php"><i class="fas fa-upload"></i><span>Manage Products</span></a>
        <a href="show_users2.php"><i class="fas fa-users"></i><span>Manage Users</span></a>
        <a href="admin_orders.php"><i class="fas fa-box"></i><span>Manage Orders</span></a>
        <a href="sales_analytics.php"><i class="fas fa-chart-line"></i><span>Check Sales</span></a>
        <a href="content_manager2.php"><i class="fas fa-cogs"></i><span>Content Manager</span></a>
        <a href="logout_admin.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<div class="container mx-auto p-6 ml-20  max-w-6xl">

    <h2 class="text-3xl font-semibold text-gray-800 mb-4">Manage Featured Products</h2>

    <!-- Show messages -->
    <?php if (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] === "max_reached"): ?>
            <div class="bg-red-500 text-white p-2 rounded-md mb-4">⚠️ You can only have 6 featured products. Remove one to add a new one.</div>
        <?php elseif ($_GET['error'] === "empty_fields"): ?>
            <div class="bg-red-500 text-white p-2 rounded-md mb-4">⚠️ Please fill in all fields.</div>
        <?php elseif ($_GET['error'] === "image_error"): ?>
            <div class="bg-red-500 text-white p-2 rounded-md mb-4">⚠️ There was an error uploading the image.</div>
        <?php elseif ($_GET['error'] === "already_featured"): ?>
            <div class="bg-red-500 text-white p-2 rounded-md mb-4">⚠️ This product is already featured.</div>
        <?php elseif ($_GET['error'] === "empty_selection"): ?>
            <div class="bg-red-500 text-white p-2 rounded-md mb-4">⚠️ Please select a product to feature.</div>
        <?php endif; ?>
    <?php elseif (isset($_GET['success']) && $_GET['success'] === "added"): ?>
        <div class="bg-green-500 text-white p-2 rounded-md mb-4">✅ Product added to featured products successfully!</div>
    <?php endif; ?>

    <!-- Form to add featured product -->
    <form action="admin_featured.php" method="POST" class="bg-white p-6 rounded-lg shadow-md mb-6">
        <label for="product_id" class="block text-gray-700 text-lg font-semibold mb-2">Select a Product to Feature</label>
        <select name="product_id" id="product_id" required class="w-full p-3 border border-gray-300 rounded-md mb-4">
            <option value="">Select a Product</option>
            <?php while ($row = $productResult->fetch_assoc()): ?>
                <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="add_featured" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700" <?php if ($totalProducts >= 6) echo 'disabled'; ?>>Add Featured Product</button>
    </form>

<!-- Featured Products Table -->
<div class="overflow-x-auto bg-white p-6 rounded-lg shadow-md">
    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 text-left text-gray-700">Image</th>
                <th class="px-4 py-2 text-left text-gray-700">Name</th>
                <th class="px-4 py-2 text-left text-gray-700">Category</th>
                <th class="px-4 py-2 text-left text-gray-700">Price</th>
                <th class="px-4 py-2 text-left text-gray-700">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="border-b">
                <td class="px-4 py-2 text-center">
                    <?php $imagePath = isset($row['image_path']) && !empty($row['image_path']) ? 'uploads/' . $row['image_path'] : 'uploads/placeholder.jpg'; ?>
                    <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" style="width: 50px; height: 50px; object-fit: cover;">
                </td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['name']); ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['category_name']); ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['price']); ?></td>
                <td class="px-4 py-2">
                    <a href="?delete=<?= $row['id']; ?>" class="bg-red-600 text-white py-1 px-3 rounded-md hover:bg-red-700" onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>



<script>
    window.onload = function () {
        const message = document.querySelector('.message');
        if (message) {
            setTimeout(() => {
                message.classList.add('fade-out');
                setTimeout(() => {
                    message.style.display = 'none';
                }, 1000);
            }, 5000);
        }
    };
</script>

</body>
</html>
<?php $conn->close(); ?>

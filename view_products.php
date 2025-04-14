<?php
session_name("admin_session");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "shopping_cart");


if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]));
}


if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($conn->query("DELETE FROM products WHERE id = $id") === TRUE) {
        header("Location: view_products.php?deleted=1");
        exit();
    } else {
        echo "Error deleting product: " . $conn->error;
    }
}

// Fetch products with category names
$sql = "SELECT products.id, products.name, products.price, products.image, 
               products.category_id, products.description, categories.category_name 
        FROM products 
        JOIN categories ON products.category_id = categories.id 
        ORDER BY products.id DESC";

$result = $conn->query($sql);

// Fetch all categories for dropdown
$categories = $conn->query("SELECT * FROM categories");

// Store categories in an array
$category_options = [];
while ($cat = $categories->fetch_assoc()) {
    $category_options[$cat['id']] = $cat['category_name'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="sidebar2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #333;
        }

        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #007bff;
            color: white;
        }

        tr:hover {
            background: #f1f1f1;
        }

        img {
            width: 50px;
            height: 50px;
            border-radius: 5px;
        }

        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .save-btn {
            background: #28a745;
            color: white;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        select {
            padding: 5px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .toast {
            display: none;
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 14px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="admin_sales.php"><i class="fas fa-tachometer-alt"></i><span>Overview</span></a>
        <a href="content_manager2.php"><i class="fas fa-cogs"></i><span>Content Manager</span></a>
        <a href="upload.php"><i class="fas fa-upload"></i><span>Manage Products</span></a>
        <a href="show_users2.php"><i class="fas fa-users"></i><span>Manage Users</span></a>
        <a href="admin_orders.php"><i class="fas fa-box"></i><span>Manage Orders</span></a>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i><span>Check Sales</span></a>
        <a href="logout_admin.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<div class="container">
    <h2>Admin - View Products</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price (₱)</th>
            <th>Category</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td contenteditable="true" id="name_<?php echo $row['id']; ?>"><?php echo $row['name']; ?></td>
                <td><textarea id="description_<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['description']); ?></textarea></td>
                <td contenteditable="true" id="price_<?php echo $row['id']; ?>"><?php echo $row['price']; ?></td>
                <td>
                    <select id="category_<?php echo $row['id']; ?>">
                        <?php foreach ($category_options as $cat_id => $cat_name) { ?>
                            <option value="<?php echo $cat_id; ?>" <?php echo ($cat_id == $row['category_id']) ? 'selected' : ''; ?>>
                                <?php echo $cat_name; ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
                <td><img src="<?php echo $row['image']; ?>" alt="Product"></td>
                <td>
                    <button class="action-btn save-btn" onclick="saveProduct(<?php echo $row['id']; ?>)">
                        <i class="fas fa-save"></i> Save
                    </button>
                    <a href="?delete=<?php echo $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<div class="toast" id="toast">✔ Product updated successfully!</div>

<script>
 function saveProduct(id) {
    let name = document.getElementById('name_' + id).innerText.trim();
    let price = document.getElementById('price_' + id).innerText.trim();
    let category = document.getElementById('category_' + id).value;
    let description = document.getElementById('description_' + id).value.trim();

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "update_product.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let response = JSON.parse(xhr.responseText);
            if (response.success) {
                showToast("✔ Product updated successfully!");
            } else {
                showToast("❌ Error updating product.");
            }
        }
    };
    xhr.send("id=" + id + "&name=" + encodeURIComponent(name) + "&price=" + encodeURIComponent(price) + "&category_id=" + encodeURIComponent(category) + "&description=" + encodeURIComponent(description));
}

function showToast(message) {
    let toast = document.getElementById("toast");
    toast.innerText = message;
    toast.style.display = "block";
    setTimeout(() => {
        toast.style.display = "none";
    }, 2000);
}
</script>

</body>
</html>

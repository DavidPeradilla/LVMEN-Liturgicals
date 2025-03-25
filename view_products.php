<?php
$conn = new mysqli("localhost", "root", "", "shopping_cart");

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete product if requested
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // Ensure ID is an integer to prevent SQL injection
    if ($conn->query("DELETE FROM products WHERE id = $id") === TRUE) {
        header("Location: view_products.php");
        exit();
    } else {
        echo "Error deleting product: " . $conn->error;
    }
}

// Fetch products with category names
$sql = "SELECT products.id, products.name, products.price, products.quantity, products.image, categories.category_name 
        FROM products 
        JOIN categories ON products.category_id = categories.id 
        ORDER BY products.id DESC";

$result = $conn->query($sql);

// Check if query execution was successful
if (!$result) {
    die("Error in SQL query: " . $conn->error);
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #ff5722;
            color: white;
        }
        img {
            width: 50px;
            height: 50px;
            border-radius: 5px;
        }
        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .save-btn {
            background: #4CAF50;
            color: white;
        }
        .delete-btn {
            background: #ff4444;
            color: white;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #ff5722;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin - View Products</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Category</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td contenteditable="true" id="name_<?php echo $row['id']; ?>"><?php echo $row['name']; ?></td>
                    <td contenteditable="true" id="price_<?php echo $row['id']; ?>"><?php echo $row['price']; ?></td>
                    <td contenteditable="true" id="quantity_<?php echo $row['id']; ?>"><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['category_name']; ?></td>
                    <td><img src="<?php echo $row['image']; ?>" alt="Product"></td>
                    <td>
                        <button class="action-btn save-btn" onclick="saveProduct(<?php echo $row['id']; ?>)">Save</button>
                        <a href="?delete=<?php echo $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <a href="upload.php" class="back-link">Back to Admin Dashboard</a>
    </div>

    <script>
        function saveProduct(id) {
            let name = document.getElementById('name_' + id).innerText;
            let price = document.getElementById('price_' + id).innerText;
            let quantity = document.getElementById('quantity_' + id).innerText;
            
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "update_product.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert("Product updated successfully!");
                }
            };
            xhr.send("id=" + id + "&name=" + encodeURIComponent(name) + "&price=" + encodeURIComponent(price) + "&quantity=" + encodeURIComponent(quantity));
        }
    </script>
</body>
</html>
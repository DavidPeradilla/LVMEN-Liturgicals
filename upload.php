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

// Fetch categories for dropdown
$categoryResult = $conn->query("SELECT * FROM categories");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category'];
    $description = $_POST['description'];
    $imagePath = "";

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif"];

        if (in_array($imageFileType, $allowed_types)) {
            $newFileName = uniqid("img_", true) . "." . $imageFileType;
            $target_file = $target_dir . $newFileName;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $imagePath = $target_file;
            } else {
                die("Error uploading file.");
            }
        } else {
            die("Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.");
        }
    } else {
        die("File upload failed. Error Code: " . $_FILES["image"]["error"]);
    }

    $sql = "INSERT INTO products (name, price, image, category_id, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsis", $name, $price,  $imagePath, $category_id, $description);
    
    if ($stmt->execute()) {
        header("Location: upload.php?success=1");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product - Admin</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ecf0f1 ;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            margin: 0;
        }


        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            margin-top: 20px;
        }

        h2 {
            color: #333;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .links {
            margin-top: 15px;
        }

        .links a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .links a:hover {
            text-decoration: underline;
        }

        select {
            width: 80%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: block;
            margin: 10px auto;
            text-align: center;
        }
    </style>
</head>
<body>


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

<div class="container mx-auto p-6 mt-6 bg-white shadow-lg rounded-lg max-w-7xl">
    <h2 class="text-2xl font-semibold text-center text-gray-800 mb-4">Upload Product</h2>

    <?php if (isset($_GET['success'])) { echo "<p class='text-green-600 text-center mb-4'>✔ Product uploaded successfully!</p>"; } ?>

    <form action="upload.php" method="POST" enctype="multipart/form-data" class="space-y-4">
    
        <input type="text" name="name" placeholder="Product Name" required class="w-full p-3 border border-gray-300 rounded-md text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">

     
        <input type="number" name="price" placeholder="Price" required class="w-full p-3 border border-gray-300 rounded-md text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">

    

    
        <select name="category" required class="w-full p-3 border border-gray-300 rounded-md text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Select Category</option>
            <?php while ($category = $categoryResult->fetch_assoc()) { ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['category_name']; ?></option>
            <?php } ?>
        </select>

        
        <textarea name="description" placeholder="Enter product description" required class="w-full p-3 border border-gray-300 rounded-md text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>

      
        <input type="file" name="image" accept="image/*" required class="w-full p-3 border border-gray-300 rounded-md text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">

        
        <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-md font-semibold hover:bg-blue-700 focus:outline-none">Upload Product</button>
    </form>

   
    <div class="mt-6 text-center space-x-4">
        <p><a href="add_category.php" class="text-blue-600 hover:underline">Add Category</a></p>
        <p><a href="view_products.php" class="text-blue-600 hover:underline -ml-3">View Products</a></p>
    </div>
</div>


</div>
</body>
</html>

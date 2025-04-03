<?php
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories for dropdown
$categoryResult = $conn->query("SELECT * FROM categories");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
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

    $sql = "INSERT INTO products (name, price, quantity, image, category_id, description) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdisis", $name, $price, $quantity, $imagePath, $category_id, $description);
    
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Style for the sidebar */
.navbar {
    width: 75px; /* Only space for the icon initially */
    height: 100vh;
    background-color: #333;
    position: fixed;
    top: 0;
    left: 0;
    transition: width 0.3s ease-in-out;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

/* Logo styles */
.navbar .logo {
    font-size: 24px;
    color: #fff;
    text-align: center;
    padding: 20px;
    background-color: #444;
    display: none; /* Hide the logo initially */
}

/* Navigation links container */
.navbar .nav-links {
    display: flex;
    flex-direction: column;
    align-items: stretch; /* Ensure links stretch to full width */
    justify-content: flex-start;
    width: 100%;
}

/* Styles for each navigation link */
.navbar .nav-links a {
    color: #fff;
    padding: 20px;
    text-decoration: none;
    display: flex;
    justify-content: center; /* Center the icon */
    align-items: center;
    font-size: 20px;
    width: 100%; /* Ensure link fills the entire width */
    transition: background-color 0.3s ease;
    box-sizing: border-box; /* Ensures padding is included in width */
}

/* Icon styling */
.navbar .nav-links a i {
    font-size: 24px; /* Adjust icon size */
}

/* Text will be hidden by default */
.navbar .nav-links a span {
    display: none;
}

/* Hover effect on links */
.navbar .nav-links a:hover {
    background-color: #555;
}

/* Style for the logout button */
.navbar .logout {
    background-color: #e74c3c;
    color: white;
}

/* Logout button hover effect */
.navbar .logout:hover {
    background-color: #c0392b;
}

/* Make the sidebar expand to show text when hovered */
.navbar:hover {
    width: 250px; /* Expand to show full navigation */
}

/* Show the logo and text when the sidebar is expanded */
.navbar:hover .logo {
    display: block;
}

.navbar:hover .nav-links a {
    padding: 15px 20px; /* Increase padding to make space for text */
    text-align: left; /* Align text to the left when expanded */
}

/* Show the text when the sidebar is expanded */
.navbar:hover .nav-links a span {
    display: inline-block;
    margin-left: 10px; /* Space between icon and text */
}

/* Ensure the logout button is aligned at the bottom */
.navbar .nav-links a.logout {
    margin-top: auto; /* Push the logout to the bottom */
}

/* Prevent background from exceeding the width */
.navbar .nav-links a.logout {
    width: 100%; /* Ensure logout button fills the width */
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
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
        <a href="content_manager.php"><i class="fas fa-cogs"></i><span>Content Manager</span></a>
        <a href="upload.php"><i class="fas fa-upload"></i><span>Manage Products</span></a>
        <a href="show_users2.php"><i class="fas fa-users"></i><span>Manage Users</span></a>
        <a href="admin_orders.php"><i class="fas fa-box"></i><span>Manage Orders</span></a>
        <a href="admin_sales.php"><i class="fas fa-chart-line"></i><span>Check Sales</span></a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<div class="container">
    <h2>Upload Product</h2>
    <?php if (isset($_GET['success'])) { echo "<p style='color: green;'>✔ Product uploaded successfully!</p>"; } ?>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="number" name="price" placeholder="Price" required>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <select name="category" required>
            <option value="">Select Category</option>
            <?php while ($category = $categoryResult->fetch_assoc()) { ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['category_name']; ?></option>
            <?php } ?>
        </select>
        <textarea name="description" placeholder="Enter product description" required></textarea>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit">Upload Product</button>
    </form>
        
    <div class="links">
        <p><a href="add_category.php"> Add Category</a></p>
        <p><a href="view_products.php"> View Products</a></p>
    </div>
</div>
</div>
</body>
</html>

<?php
include 'db2.php'; // Ensure this file sets $conn
$conn = new mysqli("localhost", "root", "", "shopping_cart");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT COUNT(id) AS total_users FROM users";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_users = $row['total_users'];

// Fetch latest users
$sql_latest = "SELECT id, first_name, last_name,email, password FROM users ORDER BY id DESC LIMIT 5";
$result_latest = $conn->query($sql_latest);


// Fetch all users
$sql = "SELECT id, first_name, last_name, email FROM users";
$result = $conn->query($sql);


include('db.php'); // Include database connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete user query
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting user!'); window.location.href='dashboard.php';</script>";
    }

    $stmt->close();
} $conn->close();

// edit users
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $FirstName = $_POST['FirstName'];
    $LastName = $_POST['LastName'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "UPDATE users SET FirstName='$FirstName', LastName='$LastName', username='$username', email='$email' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 0px 10px gray;
            border-radius: 5px;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .stat-box {
            padding: 20px;
            background:rgb(92, 95, 93);
            color: white;
            border-radius: 5px;
            width: 30%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background: #007bff;
            color: white;
        }
        

        .btn1{
            text-decoration: none;
            color: white;
            background:rgb(18, 16, 16);
            padding: 8px 15px;
            border-radius: 5px;
        }

            .navbar {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: #007bff;
                padding: 15px;
                color: white;   
                margin-top: -1.9%;
                margin-left: -1%;
                padding-top: 3%;
            }

            .navbar .logo {
                font-size: 24px;
                font-weight: bold;
            }

            .navbar .nav-links {
                display: flex;
                gap: 20px;
            }

            .navbar a {
                color: white;
                text-decoration: none;
                font-size: 16px;
            }

            .navbar a:hover {
                text-decoration: underline;
            }

            .logout {
                background-color: red;
                padding: 8px 12px;
                border-radius: 5px;
            }

            .logout:hover {
                background-color: darkred;
            }

    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="content_manager.php">Content Manager</a>
        <a href="upload.php">Manage Products</a>
        <a href="show_users2.php">Manage Users</a>
        <a href="admin_orders.php">Manage Orders</a>
        <a href="admin_sales.php">Check Sales</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

    <br><br>
    <div class="container">
    <h2>Admin Dashboard</h2>
    <div class="stats">
        <div class="stat-box">
            <h3>Total Users</h3>
            <p><?= $total_users ?></p>
        </div>
        
    </div>

   

</div>





    



    
    
</body>
</html>

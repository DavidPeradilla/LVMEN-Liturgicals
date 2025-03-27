<?php
include('db2.php'); // Include the database connection

$search = "";

// Check if a search query is submitted
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT id, FirstName, LastName, username, email FROM users 
            WHERE id = '$search'";
} else {
    $sql = "SELECT id, first_name, last_name, email FROM users";; // Fetch all users if no search query
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
        .search-bar {
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 8px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            padding: 8px 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #0056b3;
        }
        table {
            width: 80%;
            margin: auto;
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
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn1 {
            text-decoration: none;
            color: white;
            background: black;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .btn1:hover {
            background: #333;
        }
        .delete-btn {
            background: red;
            color: white;
        }
        .back-link {
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
            text-align: center;
        }
        .back-link:hover {
            text-decoration: underline;
        }
       
        a.show-all-btn{
            padding: 8px 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 10%;
        }

        a.show-all-btn:hover {
            background: #0056b3;
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
        <a href="upload.php">Manage Products</a>
        <a href="show_users2.php">Manage Users</a>
        <a href="admin_orders.php">Manage Orders</a>
        <a href="admin_sales.php">Check Sales</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

    <h2>Manage Users</h2>

    <!-- Search Bar (Search by ID) -->
    <form method="GET" action="" class="search-bar">
        <input type="text" name="search" placeholder="Enter User ID" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
        <input type="submit" value="Search">
        <a class="show-all-btn" href="show_users2.php">Show All</a>
    </form>

    <!-- User Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['first_name'] ?></td>
            <td><?= $row['last_name'] ?></td>
            <td><?= $row['email'] ?></td>
            <td>
                <a class="btn1" href="edit_users.php?id=<?= $row['id'] ?>">Edit</a> 
                <a class="btn1 delete-btn" href="delete_users.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="back-link"> 
    <a href="dashboard.php">Back to Admin Panel</a>
    </div>

</body>
</html>

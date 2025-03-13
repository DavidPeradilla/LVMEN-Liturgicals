<?php
include('db.php'); // Include the database connection


$sql = "SELECT COUNT(id) AS total_users FROM users";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_users = $row['total_users'];

// Fetch latest users
$sql_latest = "SELECT id, FirstName, LastName, username, email, password FROM users ORDER BY id DESC LIMIT 5";
$result_latest = $conn->query($sql_latest);


// Fetch all users
$sql = "SELECT id, FirstName, LastName, username, email, password FROM users";
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
    <link rel="stylesheet" type="text/css" href="styles.css"> <!-- Optional CSS -->
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
        a {
            text-decoration: none;
            color: white;
            background: #dc3545;
            padding: 10px 15px;
            border-radius: 5px;
        }
        a:hover {
            background: #c82333;
        }

        .btn1{
            text-decoration: none;
            color: white;
            background:rgb(18, 16, 16);
            padding: 8px 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<h2>Admin Panel - Manage Users</h2>
    <div class="container">
    <h2>Admin Dashboard</h2>
    <div class="stats">
        <div class="stat-box">
            <h3>Total Users</h3>
            <p><?= $total_users ?></p>
        </div>
        
    </div>

    <h3>Quick Actions</h3>
    <a href="show_users2.php">Manage Users</a>
    <a href="logout.php">Logout</a> 
    

</div>

<table>
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Password</th>
    </tr>
    
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['FirstName'] ?></td>
        <td><?= $row['LastName'] ?></td>
        <td><?= $row['username'] ?></td>
        <td><?= $row['email'] ?></td>
        <td> <? $row['password']?> </td>
       
    </tr>
    <?php endwhile; ?>
</table>



    



    
    
</body>
</html>

<?php
session_start();
include('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id = $id";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $FirstName = $_POST['FirstName'];
    $LastName = $_POST['LastName'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    $sql = "UPDATE users SET FirstName='$FirstName', LastName='$LastName', username='$username', email='$email' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: show_users2.php");
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
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px gray;
            width: 350px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        input {
            width: 90%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background:rgb(240, 0, 0);
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        input[type="submit"]:hover {
            background:rgb(138, 6, 6);
        }
        .back-link {
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Edit User</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            <label>First Name:</label>
            <input type="text" name="FirstName" value="<?= $user['FirstName'] ?>" required>
            
            <label>Last Name:</label>
            <input type="text" name="LastName" value="<?= $user['LastName'] ?>" required>
            
            <label>Username:</label>
            <input type="text" name="username" value="<?= $user['username'] ?>" required>
            
            <label>Email:</label>
            <input type="email" name="email" value="<?= $user['email'] ?>" required>

            
            
            <input type="submit" value="Update">
        </form>
        
        <a href="dashboard.php" class="back-link">Back to Admin Panel</a>
    </div>

</body>
</html>


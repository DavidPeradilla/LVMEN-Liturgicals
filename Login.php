<?php
include('db.php');  // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $admin_username = $_POST['username'];
    $admin_password = $_POST['password'];

    $correct_username = "admin";
    $correct_password = "admin123";

    if ($admin_username === $correct_username && $admin_password === $correct_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid admin credentials! <br>";
    }


    // Check if username exists in the database
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user data
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Redirect to homepage after successful login
            header("Location: LVMEN2.php"); // Replace 'index.php' with your homepage URL
            exit();  // Always call exit after header() to stop further execution
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No account found with that username!";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>

/* General Page Styling */
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    flex-direction: column;

}

/* Form Container */
.form-container {
    width: 360px;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Form Title */
h2 {
    margin-bottom: 20px;
    color: #333;
}

/* Form Fields */
label {
    display: block;
    text-align: left;
    font-weight: bold;
    margin: 3px 0 5px;
    color: #555;
}

input {
    width: 100%;
    padding: 5px;
    margin-bottom: 15px;
    border: 1px solid #0e0e0e;
    border-radius: 5px;
    font-size: 16px;
}

/* Submit Button */
input[type="submit"] {
    width: 80%;
    background: #2e9de7;
    color: rgb(0, 0, 0);
    font-size: 16px;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.5s;
}

input[type="submit"]:hover {
    background: #218838;
}

/* Sign Up Link */
p {
    margin-top: 15px;
    font-size: 14px;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}


    </style>
</head>
<body>
     
    <div class="form-container">
        <h2>Login Form</h2>
        <form method="POST" action="login.php">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Login">
        </form>
        <p>Don't have an account? <a href="register.php">Sign up here</a></p>
    </div>

</body>
</html>



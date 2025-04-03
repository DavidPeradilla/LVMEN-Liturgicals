<?php
session_start();
include('db2.php');  // Ensure correct database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Admin Login Check
    if ($email === "admin@gmail.com" && $password === "admin123") {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit();
    }

    // User Login Check
    $stmt = $login_conn->prepare("SELECT id, first_name, last_name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify hashed password (Assuming passwords are stored using password_hash)
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id']; // Store user ID
            $_SESSION['email'] = $user['email']; // Store email for add-to-cart tracking
            $_SESSION['first_name'] = $user['first_name']; 
            $_SESSION['last_name'] = $user['last_name'];

            header("Location: LVMEN.php"); // Redirect to user products page
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid login credentials.";
    }

    $stmt->close();
    $login_conn->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="LVMEN.css">
    <link rel="stylesheet" type="text/css" href="navbar2.css"> 
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
    margin-left: 0%;

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
<!--NAVBAR-->
<header>
    <a href="LVMEN.php"><img class="logo" src="Img/LVMEN Logo.jpg" style="margin-left: 1%;" width="80px" height="70px"></a>
    <nav class="navbar">
        <ul class="nav-links">
            <a href="LVMEN.php"><li>HOMEPAGE</li></a>
            <a href="AboutUs.php"><li>ABOUT US</li></a>
            <a href="user_products.php"><li>CATALOG</li></a>
            <a href="Contact.php"><li>CONTACT US</li></a>
            <a href="FAQs.php"><li>FAQs</li></a>
            <a href="profile.php">Profile</a>
            <?php if (isset($_SESSION['email'])): ?>
                <a href="logout.php" class="login-btn"><li>LOGOUT</li></a>
            <?php else: ?>
                <a href="login.php" class="login-btn"><li>LOGIN</li></a>
            <?php endif; ?>
            <a href="view_cart.php" class="cart-link">🛒</a>
        </ul>
    </nav>
</header>
     
    <div class="form-container">
        <h2>Login Form</h2>
        <form method="POST" action="login.php">
            <label for="email">Gmail:</label>
            <input type="text" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            
            <div class="submit2"> 
            <input type="submit" value="Login">
            </div>
        </form>
        
        <p>Don't have an account? <a href="register.php">Sign up here</a></p>
    </div>

</body>
</html>



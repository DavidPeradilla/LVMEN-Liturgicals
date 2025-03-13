<?php
include('db.php');  // Database connection

$success_message = "";  // Variable to store the success message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $username = $_POST['username'];
    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    // Check if username or email already exists
    $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $success_message = "<p style='color: red;'>Username or email already exists!</p>";
    } else {
        // Insert new user into the database
        $sql = "INSERT INTO users (FirstName, LastName, username, email, password) 
                VALUES ('$Fname', '$Lname', '$username', '$email', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            $success_message = "<p style='color: green;'>Registration successful!</p>";
        } else {
            $success_message = "<p style='color: red;'>Error: " . $conn->error . "</p>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>




body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 80vh;
    margin-top: 5%;
    margin-bottom: 5%;
    flex-direction: column;
}

.form-container {
    width: 360px;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

h2 {
    margin-bottom: 15px;
    color: #333;
}


label {
    display: block;
    text-align: left;
    font-weight: bold;
    margin: 10px 0 5px;
    color: #555;
}

input {
    width: 100%;
    padding: 5px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

input[type="submit"] {
    width: 80%;
    background: #007bff;
    color: white;
    font-size: 16px;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

input[type="submit"]:hover {
    background: #0056b3;
}

p {
    margin-top: 10px;
    font-size: 14px;
}

a {
    color: #28a745;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

    </style>




</head>
<body>

    <div class="form-container">
        <h2>Registration Form</h2>
        <form method="POST" action="register.php">
            <label for="Fname">First Name:</label>
            <input type="text" name="Fname" id="Fname" required>

            <label for="Lname">Last Name:</label>
            <input type="text" name="Lname" id="Lname" required>

            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Register">
        </form>

        <?php echo $success_message; ?>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

</body>
</html>

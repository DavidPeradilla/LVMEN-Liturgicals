<?php
include('db2.php');  // Database connection

$success_message = "";  // Variable to store the success message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    // Check if username or email already exists
    $sql = "SELECT * FROM users WHERE email = email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $success_message = "<p style='color: red;'>Username or email already exists!</p>";
    } else {
        // Insert new user into the database
        $sql = "INSERT INTO users (first_name, last_name,  email, password) 
                VALUES ('$Fname', '$Lname', '$email', '$password')";
        
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
    <link rel="stylesheet" type="text/css" href="LVMEN.css">
    <link rel="stylesheet" type="text/css" href="navbar2.css">
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

<!--NAVBAR-->
<header> 
<a href="LVMEN.php"> <img class="logo" src="Img/LVMEN Logo.jpg" style="margin-left: 1%;" width="80px" height="70px"></a>
  <nav class="navbar"> 
     <ul class="nav-links">
     <a href="LVMEN.php"> <li> HOMEPAGE </li> </a>  
      <a href="AboutUs.php"> <li> ABOUT US  </li> </a>
      <a href="user_products.php"> <li> CATALOG </li> </a>
      <a href="Contact.php"> <li> CONTACT US </li> </a>
      <a href="FAQs.php"> <li> FAQs </li> </a>
      <a href="profile.php"> Profile </a>

      <?php if (isset($_SESSION['email'])): ?>
      <a href="logout.php" class="login-btn"> <li> LOGOUT </li> </a>
  <?php else: ?>
      <a href="login.php" class="login-btn"> <li> LOGIN </li> </a>
  <?php endif; ?>
  <a href="view_cart.php" class="cart-link">🛒</a>
     </ul>
  </nav> 
</header>
<!--END-->
<br><br><br><br><br><br><br><br>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" type="text/css" href="LVMEN.css">
    <link rel="stylesheet" type="text/css" href="navbar2.css">
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
    <script>
        function capitalizeFirstLetter(input) {
            let words = input.value.toLowerCase().split(" ");  // Convert to lowercase first
            for (let i = 0; i < words.length; i++) {
                words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1); // Capitalize first letter
            }
            input.value = words.join(" "); // Join words back
        }
    </script>
</head>
<body>

<div class="form-container">
        <h2>Registration Form</h2>
        <form method="POST" action="register.php">
            <label for="Fname">First Name:</label>
            <input type="text" name="Fname" id="Fname" required oninput="capitalizeFirstLetter(this)">

            <label for="Lname">Last Name:</label>
            <input type="text" name="Lname" id="Lname" required oninput="capitalizeFirstLetter(this)">

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


</body>
</html>

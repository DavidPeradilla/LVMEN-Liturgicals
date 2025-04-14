<?php
session_name("user_session"); // Only if you're using a custom session name consistently
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch current user data
$stmt = $conn->prepare("SELECT first_name, last_name, email, address, contact_number FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];

    // Update user data in the database
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, address = ?, contact_number = ? WHERE email = ?");
    $stmt->bind_param("sssss", $first_name, $last_name, $address, $contact_number, $email);

    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile.";
    }
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="LVMEN.css">
    <link rel="stylesheet" type="text/css" href="navbar2.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #222;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        input {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
        .btn {
            margin-top: 20px;
            padding: 10px;
            background: rgb(141, 138, 136);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background: #005ecb;
        }
        .message {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
            color: green;
        }
        .error {
            color: red;
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
      <a href="profile.php"> PROFILE     </a>
      <a href="logout.php" class="login-btn"> <li> LOGOUT </li> </a>
      <a href="view_cart.php" class="cart-link">🛒</a>
     </ul>
  </nav> 
</header>
<!--END-->
<br> <br> <br>

<div class="container">
    <h2>Edit Profile</h2>

    <?php if (isset($success_message)) echo "<p class='message'>$success_message</p>"; ?>
    <?php if (isset($error_message)) echo "<p class='message error'>$error_message</p>"; ?>

    <form method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

        <label for="address">Address:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>

        <label for="contact_number">Contact Number:</label>
        <input type="text" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number']); ?>" required>

        <button type="submit" class="btn">Update Profile</button>
    </form>
</div>

</body>
</html>

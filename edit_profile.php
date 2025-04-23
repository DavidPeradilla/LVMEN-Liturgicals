<?php
session_name("user_session");
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email'])) {
    die("You need to log in to access this page.");
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $address = trim($_POST['address']);
    $contact_number = trim($_POST['contact_number']);

    if (empty($first_name) || empty($last_name) || empty($address) || empty($contact_number)) {
        $error_message = "All fields are required.";
    } else {
        $update_sql = "UPDATE users SET first_name = ?, last_name = ?, address = ?, contact_number = ? WHERE email = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssss", $first_name, $last_name, $address, $contact_number, $email);
        if ($stmt->execute()) {
            $success_message = "Profile updated successfully.";
        } else {
            $error_message = "Failed to update profile. Please try again.";
        }
        $stmt->close();
    }
}

// ✅ Fetch user info *after* the update so the form gets fresh data
$user_sql = "SELECT first_name, last_name, address, contact_number FROM users WHERE email = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="LVMEN.css">
    <link rel="stylesheet" type="text/css" href="navbar3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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

<!-- NAVBAR -->
<header>
  <a href="LVMEN.php">
    <img class="logo" src="Img/LVMEN Logo.jpg" style="margin-left: 1%;" width="80px" height="70px">
  </a>

  <nav class="navbar">
    <ul class="nav-links">
      <li><a href="LVMEN.php"> HOMEPAGE </a></li>
      <li><a href="AboutUs.php"> ABOUT US </a></li>
      <li><a href="user_products.php"> CATALOG </a></li>
      <li><a href="Contact.php"> CONTACT US </a></li>
      <li><a href="FAQs.php"> FAQs </a></li>

      <!-- Show profile link if logged in -->
      <li><a href="profile.php"><i class="fas fa-user"></i> </a></li>

      <!-- Show cart link -->
      <li><a href="view_cart.php" class="cart-link">
        <i class="fas fa-shopping-cart"></i>
      </a></li>

      <!-- Hide login button only if user is logged in -->
      <?php if (!isset($_SESSION['email'])): ?>
        <li><a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> LOGIN</a></li>
      <?php endif; ?>
      
    </ul>
  </nav>
</header>
<!-- END -->

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


<script>
    // Function to capitalize the first letter of each word in the input
    function capitalizeFirstLetter(inputField) {
        let value = inputField.value;
        value = value.replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
        inputField.value = value;
    }

    // Attach the function to the fields
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('input[name="first_name"]').addEventListener('input', function() {
            capitalizeFirstLetter(this);
        });
        document.querySelector('input[name="last_name"]').addEventListener('input', function() {
            capitalizeFirstLetter(this);
        });
        document.querySelector('input[name="address"]').addEventListener('input', function() {
            capitalizeFirstLetter(this);
        });
    });
</script>


</body>
</html>

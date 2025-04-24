<?php
include('db2.php');  

$success_message = "";  

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Fname = ucfirst(strtolower(trim($_POST['Fname'])));
    $Lname = ucfirst(strtolower(trim($_POST['Lname'])));
    $email = strtolower(trim($_POST['email']));

    if (!preg_match('/^(?=.*[0-9])(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $_POST['password'])) {
        $success_message = "<p style='color: red;'>Password must be at least 8 characters and include a number and a special character.</p>";
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

       
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $check = $stmt->get_result();

        if ($check->num_rows > 0) {
            $success_message = "<p style='color: red;'>Email already registered!</p>";
        } else {
           
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $Fname, $Lname, $email, $password);

            if ($stmt->execute()) {
                $success_message = "<p style='color: green;'>Registration successful!</p>";
            } else {
                $success_message = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" type="text/css" href="LVMEN.css">
    <link rel="stylesheet" type="text/css" href="navbar3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
    background: #303134;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    flex-direction: column;
    background-image: url("Img/login1.jpg");
    background-repeat: no-repeat;
    background-size: 101% 100%;
        }

        body::after {
  content: "";
  position: absolute;
  top: 78px;
  left: 0;
  height: 117%;
  width: 100%;
  background-color: rgba(0, 0, 0, 0.38);
}

        .form-container {
            width: 460px;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    z-index: 1;
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
       
        .highlight-box {
            background-color: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ffeeba;
            font-size: 14px;
            margin-top: 10px;
        }
        .highlight-box a {
            color: #007bff;
            font-weight: bold;
            text-decoration: none;
        }
        .highlight-box a:hover {
            text-decoration: underline;
        }

    
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            margin-bottom: -50px;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 900px;
        }

        .modal-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .modal-footer {
            text-align: right;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        input[type="checkbox"] {
            margin-left: -180px;
            margin-top: 20px;
        }


        .notification {
    padding: 15px 20px;
    margin: 15px 0;
    border-radius: 8px;
    font-size: 16px;
    font-family: sans-serif;
    position: relative;
    animation: fadeIn 0.5s ease-in-out;
}

.notification.success {
    background-color: #e6ffed;
    color: #256029;
    border: 1px solid #a3d9a5;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

        
    </style>
    <script>
        
        function openModal() {
            document.getElementById("termsModal").style.display = "block";
        }

      
        function closeModal() {
            document.getElementById("termsModal").style.display = "none";
        }


        function validateForm() {
            var checkbox = document.getElementById("terms");
            if (!checkbox.checked) {
                document.getElementById("termsError").style.display = "inline";
                return false;
            }
            return true;
        }
    </script>

    
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
      <li><a href="profile.php"><i class="fas fa-user"></i></a></li>

      <li>
  <a href="view_cart.php" class="cart-link" style="position: relative;">
    <i class="fas fa-shopping-cart"></i>
    <span id="cart-count" style="
        position: absolute;
        top: 2px;
        right: -3px;
        width: 18px;
        height: 18px;
        background-color: red;
        color: white;
        font-size: 11px;
        font-weight: bold;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    ">
      <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
    </span>
  </a>
</li>
      

      <?php if (isset($_SESSION['email'])): ?>
        <li class="right-align"><a href="logout.php" class="login-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a></li>
      <?php else: ?>
        <li class="right-align"><a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> LOGIN</a></li>
      <?php endif; ?>
    </ul>
  </nav> 
</header>
<!-- END -->

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

<!-- Registration Form -->
<div class="form-container">
    <h2>Registration Form</h2>
    <?php if (!empty($success_message)): ?>
    <div class="notification success">
        <?php echo $success_message; ?>
    </div>
<?php endif; ?>
    <form method="POST" action="register.php" onsubmit="return validateForm()">
        <label for="Fname">First Name:</label>
        <input type="text" name="Fname" id="Fname" required oninput="capitalizeFirstLetter(this)">

        <label for="Lname">Last Name:</label>
        <input type="text" name="Lname" id="Lname" required oninput="capitalizeFirstLetter(this)">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required style=" text-transform: lowercase;">
        <span id="email-error" style="color: red; font-size: 0.9em;"></span>



        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <div style="display: flex; align-items: left; gap: 8px; text-align: left; margin-bottom: 10px;">
               <input type="checkbox" id="showPassword" onclick="togglePassword()" style=" margin-left: -46%; margin-top: 2%;"> 
                 <label for="showPassword" style="margin-left: -50%; margin-top: 1.3%;">Show Password</label>
            </div>
              



            <input type="submit" value="Register">
            

        <div style="display: flex; align-items: center; font-size: 14px; margin-bottom: 15px;">
            <label for="terms" style=" margin-left: 0%;" >By signing up, you agree to the LVMEN <a href="javascript:void(0);" style = "text-decoration: underline; color:blue;"onclick="openModal()">Terms and Conditions</a>.</label>
            <span id="termsError" style="color: red; font-size: 12px; margin-left: 5px; display: none;">This field is required</span>
        </div>


       
    </form>

   

    <p class="highlight-box">Already have an account? <a href="login.php">Login here</a></p>
</div>

<!-- Modal -->
<div id="termsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Terms and Conditions</h2>
        </div>
        <div class="modal-body" style="text-align: justify;">
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing this website, you agree to comply with these terms and conditions. If you do not agree with these terms, please do not use our site.</p>

            <h2>2. Use of Website</h2>
            <p>Users of our site must be over 18 years old or have the consent of a parent or guardian. You agree not to misuse the website or cause harm to the system.</p>

            <h2>3. Product Information</h2>
            <p>We make every effort to display our products accurately, but we do not guarantee that descriptions, images, or other content are completely accurate, reliable, or error-free.</p>

            <h2>4. Account Security</h2>
            <p>You are responsible for maintaining the confidentiality of your account and password. Please notify us immediately if you believe there has been unauthorized access to your account.</p>

            <h2>5. Privacy</h2>
            <p>We respect your privacy. By using this website, you consent to the collection and use of your personal information as outlined in our Privacy Policy.</p>

            <h2>6. Termination of Service</h2>
            <p>We reserve the right to terminate or suspend your account or access to the website at our discretion if we believe you have violated our terms and conditions.</p>

            <h2>7. Limitation of Liability</h2>
            <p>We are not liable for any damages, losses, or expenses resulting from the use or inability to use the website or products purchased from the website.</p>

            <h2>8. Changes to Terms</h2>
            <p>We may update these terms at any time. Any changes will be posted on this page, and the date at the top will reflect the most recent update.</p>

            <h2>9. Governing Law</h2>
            <p>These terms are governed by the laws of the country in which LVMEN Liturgicals operates, and any legal disputes will be handled in the courts of that country.</p>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    var passwordInput = document.getElementById("password");
    passwordInput.type = passwordInput.type === "password" ? "text" : "password";
}

function validateForm() {
    var checkbox = document.getElementById("terms");
    var password = document.getElementById("password").value;
    var passwordPattern = /^(?=.*[0-9])(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;

    if (!checkbox.checked) {
        document.getElementById("termsError").style.display = "inline";
        return false;
    } else {
        document.getElementById("termsError").style.display = "none";
    }

    if (!passwordPattern.test(password)) {
        alert("Password must be at least 8 characters long and include at least one number and one special character.");
        return false;
    }

    return true;
}

function capitalizeFirstLetter(input) {
    let words = input.value.toLowerCase().split(" ");
    for (let i = 0; i < words.length; i++) {
        if (words[i].length > 0) {
            words[i] = words[i][0].toUpperCase() + words[i].substr(1);
        }
    }
    input.value = words.join(" ");
}


document.querySelector("form").addEventListener("submit", function (e) {
    const emailInput = document.getElementById("email");
    const emailError = document.getElementById("email-error");
    const email = emailInput.value.trim().toLowerCase();

    if (!email.endsWith("@gmail.com")) {
        e.preventDefault(); 
        emailError.textContent = "Only Gmail addresses are allowed.";
        emailInput.focus();
    } else {
        emailError.textContent = ""; 
    }
});

</script>

</body>
</html>

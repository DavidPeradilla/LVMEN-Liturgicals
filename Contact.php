<?php
session_name("user_session"); // Only if you used this in your login/logout files
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user details
$user_sql = "SELECT first_name, last_name, address, contact_number FROM users WHERE email = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html> 
    <head>
        <title> Contact </title>
        <link rel="stylesheet" type="text/css" href="LVMEN.css"> 
        <link rel="stylesheet" type="text/css" href="navbar3.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 
        <style>
#map {
    width: 2%;
    height: 10%;
    border-radius: 8px;
    position: absolute;
    bottom: 10px;
    right: 70px;
    top: 20px;
    margin-right: 100px;
}


h2 {
    text-align: center;
    color: #333;
}

form {
    margin-top: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
    color: #333;
}

input[type="text"],
input[type="email"],
textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

textarea {
    height: 150px;
}

input[type="submit"] {
    background-color: #0a7d31;
    color: #fff;
    border: none;
    padding: 12px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

input[type="submit"]:hover {
    background-color: #095e23;
}

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

* {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
  
}

body {
  
  background-image: url(p1.jpg) !important;
  background-color: rgba(0, 0, 0, 0.8);

}



section {
  position: relative;
  z-index: 3;
  padding-top: 50px;
  padding-bottom: 50px;
}

.container1 {
  max-width: 1080px;
  margin-left: auto;
  margin-right: auto;
  padding-left: 20px;
  padding-right: 20px;
  margin-top: 4%;
}

.section-header {
  margin-bottom: 65px;
  text-align: center;
  
}

.section-header h2 {
  color: #FFF;
  font-weight: bold;
  font-size: 3em;
  margin-bottom: 20px;
}

.section-header p {
  color: #FFF;
}

.row  {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
}

.contact-info {
  width: 50%;
}

.contact-info-item {
  display: flex;
  margin-bottom: 30px;
}

.contact-info-icon {
  height: 70px;
  width: 70px;
  background-color: #fff;
  text-align: center;
  border-radius: 50%;
}

.contact-info-icon i {
  font-size: 30px;
  line-height: 70px;
}

.contact-info-content {
  margin-left: 20px;
}

.contact-info-content h4 {
  color: #eb1c1c;
  font-size: 1.4em;
  margin-bottom: 5px;
}

.contact-info-content p {
  color: #FFF;
  font-size: 1em;
}

.contact-form {
  background-color: #fff;
  padding: 40px;
  width: 45%;
  padding-bottom: 20px;
  padding-top: 20px;
}

.contact-form h2 {
  font-weight: bold;
  font-size: 2em;
  margin-bottom: 15px;
  color: #333;
}

.contact-form .input-box {
  position: relative;
  width: 100%;
  margin-top: 10px;
}

.contact-form .input-box input,
.contact-form .input-box textarea{
  width: 100%;
  padding: 5px 0;
  font-size: 16px;
  margin: 10px 0;
  border: none;
  border-bottom: 2px solid #333;
  outline: none;
  resize: none;
}

.contact-form .input-box span {
  position: relative;
  left: 0;
  padding: 5px 0;
  font-size: 16px;
  margin: 10px 0;
  pointer-events: none;
  transition: 0.5s;
  color: #666;
}

.contact-form .input-box input:focus ~ span,
.contact-form .input-box textarea:focus ~ span{
  color: #e91e63;
  font-size: 12px;
  transform: translateY(-20px);
}

.contact-form .input-box input[type="submit"]
{
  width: 100%;
  background: #861f1f;
  color: #FFF;
  border: none;
  cursor: pointer;
  padding: 10px;
  font-size: 18px;
  border: 1px solid #00bcd4;
  transition: 0.5s;
}

.contact-form .input-box input[type="submit"]:hover
{
  background: #FFF;
  color: #00bcd4;
}

@media (max-width: 991px) {
  section {
    padding-top: 50px;
    padding-bottom: 50px;
  }
  
  .row {
    flex-direction: column;
  }
  
  .contact-info {
    margin-bottom: 40px;
    width: 100%;
  }
  
  .contact-form {
    width: 100%;
  }
}

#star-rating {
    direction: ltr;
    unicode-bidi: bidi-override;
    display: inline-flex;
    flex-direction: row-reverse; /* Right to left visually */
    margin-right: 68%;
  }


#star-rating input[type="radio"] {
  display: none;
}

#star-rating label {
  font-size: 30px;
  color: #ccc;
  cursor: pointer;
  transition: color 0.3s;
}

/* When a star is selected */
#star-rating input[type="radio"]:checked + label,
#star-rating input[type="radio"]:checked + label ~ label {
  color: #f5b301;
}


/* Hover effect */
#star-rating label:hover,
#star-rating label:hover ~ label {
  color: #f5b301;
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
      <li><a href="profile.php"><i class="fas fa-user"></i></a></li>
      <li>
        <a href="view_cart.php" class="cart-link">
          <i class="fas fa-shopping-cart"></i>
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

<br><br>

<section id="contactForm">
    <div class="section-header">
      <div class="container1">
        <h2>Contact Us</h2>
        <p>Feel free to reach out to us via our contact form or directly through email or phone. We're here to assist you with any questions, concerns, or feedback you may have, and our team will be delighted to assist you promptly any inquiries or feedback you may have!</p>
      </div>
    </div>
    
    <div class="container1">
      <div class="row">
        
        <div class="contact-info">
          <div class="contact-info-item">
            <div class="contact-info-icon">
              <i class="fas fa-home"></i>
            </div>
            
            <div class="contact-info-content">
              <h4>Address</h4>
              <p>Region IV-A Calabarzon, <br/> Bacoor Cavite City <br/></p>
            </div>
          </div>
          
          <div class="contact-info-item">
            <div class="contact-info-icon">
              <i class="fas fa-phone"></i>
            </div>
            
            <div class="contact-info-content">
              <h4>Phone</h4>
              <p>+123-456-7890</p>
            </div>
          </div>
          
          <div class="contact-info-item">
            <div class="contact-info-icon">
              <i class="fas fa-envelope"></i>
            </div>
            
            <div class="contact-info-content">
              <h4>E-mail</h4>
             <p>contact@lvmen24.com</p>
            </div>
          </div>
        </div>
        
          
        <div class="contact-form">

          <form id="contactForm" action="https://api.web3forms.com/submit" method="POST" >
            <input type="hidden" name="access_key" value="f650f1cb-4844-403c-b07a-674ae7eb247f">
            <h2>Send Message</h2>
            
            <div class="input-box">
              <label for="subject">Subject:</label>
        <select id="subject" name="subject" required>
            <option value="Feedback: New Feedback from Contact Form">Feedbacks</option>
            <option value="Inquiry: New Inquiries from Contact Form">Inquiry</option>
        </select></div>

            <div class="input-box">
            <input type="text" name="recipient_name" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" readonly>
            </div>
            
            <div class="input-box">
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>
            
            <div class="input-box">
              <textarea type="text" id="message" name="Full Name: " placeholder="Type your Message..." value=" "></textarea>
            </div>

           <div id="star-rating">
            <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
            <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
            <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
            <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
            <input type="radio" id="star5" name="rating" value="5"><label for="star5">★</label>
           </div>

            <div class="input-box">
              <input type="submit" value="Send" name="">
            </div>
          </form>
        </div>
        
      
        
      </div>
    </div>


    <script>
  const subjectSelect = document.getElementById('subject');
  const starRatingBox = document.getElementById('star-rating');

  subjectSelect.addEventListener('change', function () {
    if (this.value === "Feedback: New Feedback from Contact Form") {
      starRatingBox.style.display = 'flex';
    } else {
      starRatingBox.style.display = 'none';
      const selectedStar = document.querySelector('input[name="rating"]:checked');
      if (selectedStar) selectedStar.checked = false;
    }
  });

  // Check on load
  window.addEventListener('DOMContentLoaded', () => {
    if (subjectSelect.value === "Feedback: New Feedback from Contact Form") {
      starRatingBox.style.display = 'flex';
    } else {
      starRatingBox.style.display = 'none';
    }
  });
</script>
  </section>

  <footer style="margin-top: 1.55%; height: 300px;">
    <div class="container" style="height: 50%;">
        <div class="footer-right">  
            <h3>GET IN TOUCH</h3>
            <div class="social">
              <a href="https://www.facebook.com/LvmenLiturgicalVestments" target="_blank" ><img src="Img/facebook.png" alt="Facebook"></a>
              <a href="#"><img src="Img/twitter.png" target="_blank" alt="Twitter"></a>
              <a href="https://www.instagram.com/explore/locations/108212715189138/dankatsu/" target="_blank" ><img src="Img/instagram.png" alt="Instagram"></a>
            </div>
        </div>
    </div>
    <div id="map" style="width:50%; height:200px;">
      
      <script>
        function myMap() {
        var mapProp= {
          center:new google.maps.LatLng(51.508742,-0.120850),
          zoom:5,
        };
        var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
        }

        document.getElementById('contactForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const status = document.getElementById('status');

        showModal('Sending...', 'Your message is being sent.');

        try {
            const response = await fetch(form.action, {
                method: form.method,
                body: new FormData(form),
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                showModal('Thank you!', 'Thank you for your message!');
                form.reset();
            } else {
                showModal('Error', 'Something went wrong. Please try again later.');
            }
        } catch (error) {
            showModal('Error', 'There was a problem submitting your form. Please try again later.');
        }
    });

    function showModal(title, message) {
        hideModal();  // Ensure any existing modal is removed
        const modal = document.createElement('div');
        modal.classList.add('modal');
        modal.innerHTML = `
            <div class="modal-content">
                <h2>${title}</h2>
                <p>${message}</p>
            </div>
        `;
        document.body.appendChild(modal);

        // Automatically remove the modal after 3 seconds
        setTimeout(function() {
            hideModal();
        }, 3000);
    }

    function hideModal() {
        const modal = document.querySelector('.modal');
        if (modal) {
            modal.remove();
        }
    }

    
        </script>
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d4507.146926638975!2d120.94300497032161!3d14.443054994990002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sph!4v1717421110521!5m2!1sen!2sph" width="750" height="250" style="border: 10px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</footer>
<script src="https://kit.fontawesome.com/c32adfdcda.js" crossorigin="anonymous"></script>

</body>
</html>
<?php
session_name("user_session"); // Only if you're using a custom session name consistently
session_start();

$conn = new mysqli("localhost", "root", "", "shopping_cart");


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Fetch featured products with full details
$query = "
    SELECT p.id, p.name, p.price, p.image, p.description, c.category_name
    FROM featured_products fp
    JOIN products p ON fp.id = p.id
    JOIN categories c ON p.category_id = c.id
";
$result = $conn->query($query);

// Fetch latest products (e.g., latest 4)
$sql_latest = "SELECT * FROM products ORDER BY created_at DESC LIMIT 3";
$result_latest = $conn->query($sql_latest);



// Query to fetch active slideshow images
$sql_slideshow = "SELECT * FROM slideshow_images WHERE active = 1";
$result_slideshow = $conn->query($sql_slideshow);

// Check if there are any slides
if ($result_slideshow->num_rows > 0) {
    while ($slide = $result_slideshow->fetch_assoc()) {
        
        echo '<div class="mySlides fade">';
        echo '<div class="numbertext">1/' . $result_slideshow->num_rows . '</div>';
        echo '<img src="' . htmlspecialchars($slide['image_path']) . '" style="width: 100%;">';
        echo '</div>';
    }
} else {
    echo "No images found in the slideshow.";
}




?> 

<!DOCTYPE html>
<html> 
    <head>
        <title> LVMEN Liturgicals </title>
        <link rel="stylesheet" type="text/css" href="LVMEN.css"> 
        <link rel="stylesheet" type="text/css" href="navbar3.css"> 
        <link rel="stylesheet" type="text/css" href="footer3.css"> 
        <link rel="stylesheet" type="text/css" href="card.css"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
        <form action="/LVMEN Liturgicals/php files/login.php" method="POST">
        <style>

 
.slideshow-container {
    position: relative;
    width: 100%;
    height: 600px; /* Set this to your desired banner height */
    overflow: hidden;
}

.mySlides {
    display: none;
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
}

.slide-image {
    width: 100%;
    height: 100%;
    object-fit: cover; /* This makes sure the image fills the area and maintains aspect ratio */
}


.hero-overlay {
    position: absolute;
    top: 30%;
    left: 49.8%;
    transform: translate(-50%, -30%);
    color: #fff;
    text-align: center;
    z-index: 2;
    margin-top: 26.5%;
    font-size: 35px;
    text-decoration: none;
    font-family: "Gill Sans", "Gill Sans MT", "Trebuchet MS", sans-serif;
    
}


/* Optional fade animation */
.fade {
    animation: fadeEffect 1s;
}

@keyframes fadeEffect {
    from { opacity: 0.5; }
    to { opacity: 1; }
}



.prev, .next {
    cursor: pointer;
    position: absolute;
    top: 50%;
    padding: 16px;
    margin-top: -22px;
    color: white;
    font-weight: bold;
    font-size: 18px;
    user-select: none;
    transition: 0.6s ease;
    display: block; /* force always visible */
    z-index: 1; /* ensure above images */
}

.next {
    right: 0;
}

body{ 
    background: #303134;
}

.headline, .headline2 {
    font-family: 'Playfair Display', serif;
   font-size: 35px;
    font-weight: 700;
    color: white;
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease, transform 0.6s ease;
    text-align: center;
}

.headline.visible, .headline2.visible {
    opacity: 1;
    transform: translateY(0);
}

.card {
    width: 350px;
    height: 560px;
    border-radius: 16px; /* Rounded corners */
    background: #fff; /* White background */
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); /* Soft shadow */
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-align: center;
    transition: all 0.4s ease;
    transform: translateY(30px); /* Start position */
    opacity: 0; /* Start hidden */
    animation: fadeInUp 0.8s forwards; /* Add animation */
    padding: 20px; /* Inner padding */
    position: relative;
}



.card img {
    height: 400px;
    object-fit: cover;
    width: 100%;
    transition: transform 0.3s ease;
}


.card:hover img {
    transform: scale(1.05);
}

.card h3 {
    font-size: 18px;
   margin-bottom: 10px;
    padding: 0 10px;
    height: 50px;
    overflow: hidden;
}


.card .price {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 15%;
    color: #333;
}

.card.visible {
        opacity: 1;
        transform: translateY(0);
    }



.row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}

.cart-link {
    position: relative;
    display: inline-block;
    font-size: 24px; 
}

#cart-icon {
    width: 30px;
    height: 30px; 
}

.hero-section {
    width: 100%;
}

.hero-background {
    width: 100%;
    height: 650px; /* adjust as needed */
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-overlay {
    position: absolute;
    text-align: center;
    color: white;
    z-index: 2;
}

.hero-btn {
    padding: 10px 40px;
    background-color: #fff;
    color: #000;
    text-decoration: none;
    display: inline-block;
}


.hero-btn:hover {

    color: blck;

}

.card button{
    background-color: #ff5733;
        color: white;
        border: none;
        padding: 8px 15px;
        cursor: pointer;
        font-size: 14px;
        border-radius: 5px;
        transition: background 0.3s;
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




<section class="hero-section">
    <?php
    $sql_banner = "SELECT * FROM slideshow_images WHERE active = 1 LIMIT 1";
    $result_banner = $conn->query($sql_banner);

    if ($result_banner && $result_banner->num_rows > 0) {
        $row = $result_banner->fetch_assoc();
        echo '<div class="hero-background" style="background-image: url(' . htmlspecialchars($row['image_path']) . ');">';
    } else {
        echo '<div class="hero-background" style="background-image: url(images/default-banner.jpg);">';
    }
    ?>
        <div class="hero-overlay">
            <a href="user_products.php" class="hero-btn"></a>
        </div>
    </div> <!-- end of hero-background -->
</section>





<script>
let slideIndex = 0;

function showSlidesAuto() {
    const slides = document.getElementsByClassName("mySlides");

    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }

    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1; }

    if (slides.length > 0) {
        slides[slideIndex - 1].style.display = "block";
    }

    setTimeout(showSlidesAuto, 5000); // Change image every 5 seconds
}

document.addEventListener("DOMContentLoaded", showSlidesAuto);
</script>



<!--LATEST PRODUCTS-->

</br></br></br></br>

<p class="headline" ALIGN="center"> LATEST PRODUCTS </p>
<div class="row">
    <?php while ($latest = $result_latest->fetch_assoc()): ?>
        <div class="card" style="margin: 1%;">
        <img src="<?= htmlspecialchars($latest['image']); ?>" alt="<?= htmlspecialchars($latest['name']); ?>">
            <h3> <?= htmlspecialchars($latest['name']); ?> </h3>
        </div>
    <?php endwhile; ?>
</div>




</br></br>

<!-- FEATURED PRODUCTS -->
<p class="headline headline2" align="center"> FEATURED PRODUCTS </p>
<div class="row" style="display: flex; flex-wrap: wrap; justify-content: center;">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card" style="width: 300px; margin: 1%; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; align-items: center;">
            <img src="<?= htmlspecialchars($row['image']); ?>" alt="<?= htmlspecialchars($row['name']); ?>" style="width: 100%; height: 300px; object-fit: cover; border-radius: 8px;">
            <h3 style="margin: 15px 0; font-size: 18px; text-align: center;"><?= htmlspecialchars($row['name']); ?></h3>
            <p style="font-weight: bold; font-size: 16px; color: #333;">₱<?= number_format($row['price'], 2); ?></p>
        </div>
    <?php endwhile; ?>
</div>
<!-- END -->



<div style="text-align: center; margin-top: 30px;">
    <a href="user_products.php" style="
        display: inline-block;
        padding: 20px 84px;
        background-color: white;
        color: #837953;
        border: 2px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        transition: background-color 0.3s, color 0.3s;
    " onmouseover="this.style.backgroundColor='#837953'; this.style.color='white';" onmouseout="this.style.backgroundColor='white'; this.style.color='#837953';">
        View Full Catalog
    </a>
</div>

</br></br> 

<div class="backgroundcolor" > 
  
  <p class="header1" ALIGN="left"> 2 <br> </p>
  <p class="headerstyle" ALIGN="left"> YEARS ON THE MARKET  </p>

   
 <p class="header2" ALIGN="left"> 39 </p>
 <p class="headerstyle2" ALIGN="left"> PRODUCTS OFFERS  </p>
 
 </div>


<!--FOOTER-->
<footer class="site-footer">
  <div class="container">
    <div class="get-in-touch">
      <h4>Get in Touch</h4>
      <div class="social-icons">
        <a href="https://www.facebook.com/LvmenLiturgicalVestments" target="_blank">
          <img src="Img/facebook.png" alt="Facebook">
        </a>
        <a href="#" target="_blank">
          <img src="Img/twitter.png" alt="Twitter">
        </a>
        <a href="https://www.instagram.com/explore/locations/108212715189138/dankatsu/" target="_blank">
          <img src="Img/instagram.png" alt="Instagram">
        </a>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <p>&copy; 2025 LVMEN Liturgicals. All Rights Reserved.</p>
    <p><a href="Contact.php">Contact Us</a> | <a href="/privacy-policy">Terms and Condition</a></p>
  </div>
</footer>

 <!--END--> 



 <script>
const cards = document.querySelectorAll('.card');

// Function to handle visibility on scroll and initial load
const handleCardVisibility = () => {
    const triggerBottom = window.innerHeight * 0.9;

    cards.forEach(card => {
        const cardTop = card.getBoundingClientRect().top;
        const cardBottom = card.getBoundingClientRect().bottom;

        // Check if the card is in the viewport
        if (cardTop < triggerBottom && cardBottom > 0) {
            card.classList.add('visible');
        } else {
            card.classList.remove('visible');
        }
    });
};

// Run the visibility check on page load
document.addEventListener('DOMContentLoaded', handleCardVisibility);

// Run the visibility check on scroll
window.addEventListener('scroll', handleCardVisibility);

 // Select both headline elements
const headlines = document.querySelectorAll('.headline, .headline2');  // Selects both .headline and .headline2

// Function to handle fade-in effect when scrolling
const toggleHeadlineVisibility = () => {
    const triggerBottom = window.innerHeight * 0.9;  // Trigger when 90% of the viewport height is reached
    
    // Iterate through each headline and check its position
    headlines.forEach(headline => {
        const headlineTop = headline.getBoundingClientRect().top;  // Get the position of the headline element
        
        // Check if the headline is in view
        if (headlineTop < triggerBottom) {
            headline.classList.add('visible');  // Add the 'visible' class to trigger fade-in
        } else {
            headline.classList.remove('visible');  // Remove the 'visible' class to fade it out
        }
    });
};

// Listen for the 'scroll' event to trigger the fade-in/fade-out effect
window.addEventListener('scroll', toggleHeadlineVisibility);

// Run the function once on page load to handle the initial visibility
window.addEventListener('load', toggleHeadlineVisibility);




    
</script>



</body>
</html>
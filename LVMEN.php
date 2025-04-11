<?php
session_name("user_session"); // Only if you're using a custom session name consistently
session_start();

$conn = new mysqli("localhost", "root", "", "shopping_cart");


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch featured products
$sql = "SELECT * FROM featured_products";
$result = $conn->query($sql);


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
        <link rel="stylesheet" type="text/css" href="navbar2.css"> 
        <link rel="stylesheet" type="text/css" href="footer.css"> 
        <form action="/LVMEN Liturgicals/php files/login.php" method="POST">


        <style>
    /* Slideshow container */
    .slideshow-container {
        position: relative;
        max-width: 100%;
        margin: auto;
        overflow: hidden;
    }

    /* Hide all images by default */
    .mySlides2 {
        display: none;
    }

    /* Image styling */
    .slide-image {
        width: 100%; /* Make the images fill the container */
        height: auto; /* Maintain aspect ratio */
        object-fit: cover; /* Ensures images cover the entire container without distorting */
    }

    /* Dots for slide indicators */
    .dot {
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #bbb;
        border-radius: 50%;
        display: inline-block;
        transition: background-color 0.6s ease;
    }

    /* Style for active dot */
    .active {
        background-color: #717171;
    }

    /* Previous and next buttons */
    .prev, .next {
        cursor: pointer;
        position: absolute;
        top: 50%;
        padding: 16px;
        margin-top: -22px;
        color: white;
        font-weight: bold;
        font-size: 18px;
        transition: 0.3s;
        border-radius: 0 3px 3px 0;
        user-select: none;
    }

    .next {
        right: 0;
        border-radius: 3px 0 0 3px;
    }

    .prev:hover, .next:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }

    .card {
    width: 350px;
    height: 480px;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    text-align: center;
    background-color: white;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.card img {
    height: 350px;
    object-fit: cover;
    width: 100%;
}

.card h3 {
    font-size: 18px;
    margin: 10px 0;
    padding: 0 10px;
    height: 50px;
    overflow: hidden;
}

.card .price {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #333;
}


.row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}


</style>
    </head>
<body>
<!-- NAVBAR -->
<header> 
<a href="LVMEN.php"> <img class="logo" src="Img/LVMEN Logo.jpg" style="margin-left: 1%;" width="80px" height="70px"></a>
  <nav class="navbar"> 
     <ul class="nav-links">
     <a href="LVMEN.php"> <li> HOMEPAGE </li> </a>  
      <a href="AboutUs.php"> <li> ABOUT US  </li> </a>
      <a href="user_products.php"> <li> CATALOG </li> </a>
      <a href="Contact.php"> <li> CONTACT US </li> </a>
      <a href="FAQs.php"> <li> FAQs </li> </a>
      <a href="profile.php"> PROFILE </a>

      <?php if (isset($_SESSION['email'])): ?>
      <a href="logout.php" class="login-btn"> <li> LOGOUT </li> </a>
      <?php else: ?>
      <a href="login.php" class="login-btn"> <li> LOGIN </li> </a>
      <?php endif; ?>
<a href="view_cart.php" class="cart-link">🛒</a>
     </ul>
  </nav> 
</header>
<!-- END -->


<!-- SLIDESHOW -->
<div class="slideshow-container">
    <?php
    // Fetch active slideshow images
    $sql_slideshow = "SELECT * FROM slideshow_images WHERE active = 1";
    $result_slideshow = $conn->query($sql_slideshow);
    
    if ($result_slideshow->num_rows > 0) {
        $counter = 1;
        while ($slide = $result_slideshow->fetch_assoc()) {
            echo '<div class="mySlides fade">';
            echo '<div class="numbertext">' . $counter . '/' . $result_slideshow->num_rows . '</div>';
            echo '<img src="' . htmlspecialchars($slide['image_path']) . '" class="slide-image">';
            echo '</div>';
            $counter++;
        }
    } else {
        echo "No slideshow images found.";
    }
    ?>
    
    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
    <a class="next" onclick="plusSlides(1)">&#10095;</a>
</div>

<div style="text-align:center">
    <?php
    // Display dots for each active slide
    for ($i = 1; $i <= $result_slideshow->num_rows; $i++) {
        echo '<span class="dot"></span>';
    }
    ?>
</div>

<!-- END -->

<script>
    let slideIndex = 0;
    let slides = document.getElementsByClassName("mySlides");
    let dots = document.getElementsByClassName("dot");

    // Initialize the slideshow to start at the first slide
    showSlides();

    function showSlides() {
        let i;
        
        // Hide all slides
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";  
        }

        slideIndex++;
        
        // If we've reached the end, reset to the first slide
        if (slideIndex > slides.length) {
            slideIndex = 1;
        }

        // Reset dot styling
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active", "");
        }

        // Display the current slide and highlight the corresponding dot
        slides[slideIndex-1].style.display = "block";
        dots[slideIndex-1].className += " active";

        // Change the slide every 3 seconds
        setTimeout(showSlides, 3000);
    }

    function plusSlides(n) {
        slideIndex += n;

        // If we've reached the end, reset to the first slide
        if (slideIndex > slides.length) {
            slideIndex = 1;
        }

        if (slideIndex < 1) {
            slideIndex = slides.length;
        }

        showSlides();
    }
</script>



    <div class="backgroundcolor" > 
  
     <p class="header1" ALIGN="left"> 2 <br> </p>
     <p class="headerstyle" ALIGN="left"> YEARS ON THE MARKET  </p>

      
    <p class="header2" ALIGN="left"> 39 </p>
    <p class="headerstyle2" ALIGN="left"> PRODUCTS OFFERS  </p>
    
    </div>
    
 <!--BEST SELLERS-->
</br>
</br>
 <p class="headline" ALIGN="center"> FEATURED PRODUCTS </p> 

 <div class="row">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card" style="margin: 1%;">
            <img src="<?= htmlspecialchars($row['image_path']); ?>" style="width: 100%;">
            <h3> <?= htmlspecialchars($row['name']); ?> </h3>
            <p class="price"> <?= htmlspecialchars($row['description']); ?> </p>
        </div>
    <?php endwhile; ?>
</div>

<?php $conn->close(); ?>
<!--END-->

</br>
</br> 


<!--FOOTER-->
<footer>
   <div class="container">
    <div class="get-in-touch">
              <h4>Get in Touch</h4>
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
   
   <div class="footer-bottom">
       <p>&copy; 2025 LVMEN Liturgicals. All Rights Reserved.</p>
       <p><a href="Contact.php">Contact Us</a> | <a href="/privacy-policy">Terms and Condition</a></p>
   </div>
</footer>
 <!--END--> 


</body>
</html>
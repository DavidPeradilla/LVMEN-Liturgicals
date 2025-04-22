<?php
session_name("user_session"); // Only if you're using a custom session name consistently
session_start();

?>

<!DOCTYPE html>
<html> 
    <head>
        <title> About Us </title>
        <link rel="stylesheet" type="text/css" href="LVMEN.css"> 
        <link rel="stylesheet" type="text/css" href="navbar3.css"> 
        <link rel="stylesheet" type="text/css" href="footer3.css"> 
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <style>
            body {
              font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  }
  
  html {
    box-sizing: border-box;
  }
  
  *, *:before, *:after {
    box-sizing: inherit;
  }
  
  .column {
    float: left;
    width: 33.3%;
    margin-bottom: 16px;
    padding: 0 8px;
  }
  
  .card3 {
    box-shadow: 0 4px 8px 0 rgba(63, 62, 62, 0.2);
    margin: 8px;
    background-color: rgb(235, 234, 234);
  }
  
  .about-section {
    padding: 50px;
    text-align: center;
    background-color: #4b2525;
    color: white;
    position: relative;
  }
  
  .container3 {
    padding: 0 16px;
  }
  
  .container3::after, .row2::after {
    content: "";
    clear: both;
    display: table;
  }
  
  .title {
    color: grey;
  }
  
  .button {
    border: none;
    outline: 0;
    display: inline-block;
    padding: 8px;
    color: white;
    background-color: #000;
    text-align: center;
    cursor: pointer;
    width: 100%;
    
  }
  
  .button:hover {
    background-color: #555;
  }
  
  @media screen and (max-width: 650px) {
    .column {
      width: 100%;
      display: block;
    }
  }


  *{
margin:0px; padding:0px;
box-sizing: border-box;
}

.about-us{
padding:80px 0px;
}

.container{
max-width: 1200px;
margin:0 auto;
padding:0 20px;
}

.row{
display: flex;
flex-wrap: wrap;
}

.flex{
flex:0 0 50%;
max-width: 50%;
padding:0 20px;
}

.about-us h2{
font-size: 45px; 
margin-bottom: 20px; 
color:#333;
margin-top: 50px;
}

.about-us h3{
font-size: 22px; 
color:#888; 
margin-bottom: 8px;
}

.about-us p{
font-size: 18px;
Line-height: 1.5; 
color:#333;
margin-bottom: 20px;
}

.about-us img{
display: block;
max-width: 100%;
height: auto; 
margin:0 auto;
margin-top: 120px;
size: 50px;
} 


.social-links{
margin-bottom: 20px;
}

.social-links a{
display: inline-block; width:40px;
height: 40px;
Line-height: 40px;
text-align: center;
border-radius: 50%;
margin-right: 10px;
color:#fff;
background-color: #333;
box-shadow: 0 2px 5px rgba(0,0,0,0.3); 
transition: all 0.4s ease;
}

.social-links a:hover{
transform: translateY(-3px);
} 


.btn{
text-decoration: none; 
color:#fff;
display: inline-block; 
padding:10px 20px; 
font-size: 18px; 
font-weight: bold;
text-transform: uppercase;
border-radius: 5px;
background-color: #333;
box-shadow: 0 2px 5px rgba(0,0,0,0.3); 
transition: all 0.3s ease;
}

.btn:hover{
transform: translateY(-3px);
}

@media screen and (max-width: 768px){
.row{
  flex-direction: column;
}
.flex{
max-width: 100%;
}
.about-us h2{
font-size: 31px;
}
.about-us p{
font-size: 16px;
}
.social-links a{
width:30px; 
height: 30px; 
Line-height: 30px; 
font-size: 14px; 
margin-right: 5px 
}
.btn{
font-size: 16px; 
padding:8px 16px;
margin-bottom: 30px ;
}
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



<div class="about-us"> 
  <div class="container"> 
    <div class="row">
      <div class="flex">
        <h2>About Us</h2>
        <h3>Discover Our Team's Story</h3>
        <div style="text-align: justify;">
        <p>We are LVMEN aims to provide beautiful and high-quality but, affordable liturgical products from Europe and Philippines.</p>
        <p>LVMEN Liturgical started in July 2021. Our Advocacy is to offer the best to God in the form of beautiful vestments, lace, and other liturgicals products that we sell. Our products are hand-made that is made form Europe. LVMEN offers to deliver nationwide.</p>
        <p>LVMEN is affiliated with different organizations in order to help those in needs some of their profits goes to charity, monastries/convent, and donations. </p>
        </div>
        <div class="social-links">
          <a href="https://www.facebook.com/LvmenLiturgicalVestments" target="_blank" ><i class="fab fa-facebook-f"></i></a> 
          <a href=""><i class="fab fa-twitter"></i></a> 
          <a href="https://www.instagram.com/sympaticou/" target="_blank" ><i class="fab fa-instagram"></i></a>
        </div>
        <!-- <a href="" i class="btn">Learn More</a> -->
      </div>
      <div class="flex">
        <img src="Img/LVMEN About Us.png" >
      </div>
    </div>
    </div>
  </div>


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
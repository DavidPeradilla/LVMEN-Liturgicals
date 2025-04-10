<?php
session_start();

?>

<!DOCTYPE html>
<html> 
    <head>
        <title> LVMEN Liturgicals </title>
        <link rel="stylesheet" type="text/css" href="LVMEN.css">
        <link rel="stylesheet" type="text/css" href="navbar2.css"> 
        <link rel="stylesheet" type="text/css" href="footer.css"> 
        
    </head>
<body style>
  
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
<!--END-->

  <h1 class="h2" ALIGN="left"  style="margin-left: 1%; margin-top: 1%; padding-top: 100px;"> Frequently Asked Questions </h1>

  <button class="accordion"> <b> How many weeks/days does it take to received my order? </b></button>
<div class="panel">
  <p>It depends on the product but casually it will take 3-4 weeks before you will receive your order.</p>
</div>

<button class="accordion"><b>What are the available payment methods? </b></button>
<div class="panel">
  <p> The available payment methods are thru G-cash and Bank Transfer.</p>
</div>

<button class="accordion"><b>What are the trusted courier company?</b></button>
<div class="panel">
  <p>Our trusted companies are LBC and Lalamove. </p>
</div>

<button class="accordion"><b>Do you accept orders Nationwide?</b></button>
<div class="panel">
  <p>Yes, LVMEN Liturgicals offers to deliver Nationwide. </p>
</div>

<button class="accordion"><b>How will i find the right Measurements   for me?</b></button>
<div class="panel">
  <p> You may submit your sizes thru messaging the Facebook Page. </p>
</div>

<button class="accordion"><b>Can I change or cancel my order after it’s been placed?</b></button>
<div class="panel">
  <p> No, because we have "No Refund and No Replace" policy. </p>
</div>

<button class="accordion"><b>Where does the Surplices and Laces were made from?</b></button>
<div class="panel">
  <p> Our Surplices and Laces were made from Europe. </p>
</div>

<button class="accordion"><b>What fabric is used to make Surplices and Laces?</b></button>
<div class="panel">
  <p> The fabric that is used to make Surplices and Laces are made from Linen Fabric. </p>
</div>

<button class="accordion"><b>What metal are used to make Chalice?</b></button>
<div class="panel" style="margin-bottom: 1%;">
  <p> Chalices are made of silver or other semi-precious metals. </p>
</div>


<script>
    var acc = document.getElementsByClassName("accordion");
    var i;
    
    for (i = 0; i < acc.length; i++) {
      acc[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight) {
          panel.style.maxHeight = null;
        } else {
          panel.style.maxHeight = panel.scrollHeight + "px";
        } 
      });
    }
    </script>

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
<!DOCTYPE html>
<html> 
    <head>
        <title> LVMEN Liturgicals </title>
        <link rel="stylesheet" type="text/css" href="LVMEN.css"> 
    </head>
<body>
<!--NAVBAR-->
<header> 
  <a href="LVMEN2.html"> <img class="logo" src="Img/LVMEN Logo.jpg" style="margin-left: 1%;"></a>
  <nav class="navbar"> 
    <ul class="nav-links">
     <a href="LVMEN2.html"> <li> HOMEPAGE </li> </a>  
     <a href="AboutUs.html"> <li> ABOUT US  </li> </a>
     <a href="Catalog.html"> <li> CATALOG </li> </a>
     <a href="Contact.html"> <li> CONTACT US </li> </a>
     <a href="FAQs.html"> <li> FAQs </li> </a>
     <a href="login.php" class="login-btn"> <li> LOGOUT </li> </a>
    </ul>
 </nav> 
</header>
<!--END-->


<!--SLIDESHOW-->
<div class="padding"> 
<div class="slideshow-container">
    <div class="mySlides fade"> 
      <div class="numbertext"> 1/3 </div>
      <img src="Img/LVMEN BANNER 2 (3).png" style="width: 100%;">
    </div>
    
    <div class="mySlides fade"> 
        <div class="numbertext"> 2/3 </div>
        <img src="Img/LVMEN Banner Website.png" style="width: 100%;">
    </div>

      <div class="mySlides fade"> 
        <div class="numbertext"> 3/3 </div>
        <img src="Img/LVMEN Slideshow.png" style="width: 100%;">
      </div>
    
    <a class="prev" onclick="plusSlides(-1)" style="color: white;"><</a>
    <a class="next" onclick="plusSlides(1)">></a>
</div>
<div style="text-align:center">
  <br>
  <span class="dot"></span> 
  <span class="dot"></span> 
  <span class="dot"></span> 
</div>
</div>
<!--END-->
<br>


<script>
  let slideIndex = 0;
  showSlides();
  
  function showSlides() {
    let i;
    let slides = document.getElementsByClassName("mySlides");
    let dots = document.getElementsByClassName("dot");
    for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";  
    }
    slideIndex++;
    if (slideIndex > slides.length) {slideIndex = 1}    
    for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
    }
    slides[slideIndex-1].style.display = "block";  
    dots[slideIndex-1].className += " active";
    setTimeout(showSlides, 3000); // 
  }
  </script>

    <div class="card2"> 
      <a href="Catalog.html" target="_self"><img class="b" src="Img/CARD PRODUCTS.png" style="width: 22%; margin-left: 25%;">  </a>
      <a href="Collections.html" target="_self"> <img class="b" src="Img/POPE PIUS X PREMIUM.png" style="width: 22%; margin-left: 5%;"> </a> 
    </div>

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
  <div class="card" style="margin-top: 1%;"> 
    <img src="Img/S5- Surplice.jpg" style="width: 100%;"> 
    <h3> S5 - Surplice </h3>
    <p class="price"> ₱5,500 </p>
   </div>

   <div class="card" style="margin-left: 0%;"> 
    <img src="Img/S6- Surplice.jpg" style="width: 100%;"> 
    <h3> S6 - Surplice </h3>
    <p class="price"> ₱5,500 </p>
   </div>

   <div class="card" style="margin-left: 0%;"> 
    <img src="Img/S7- Surplice.jpg" style="width: 100%;"> 
    <h3> S7 - Surplice </h3>
    <p class="price"> ₱5,500 </p>
   </div>

</div>

<div class="row" style="margin-top: 2%;">
  <div class="card" style="margin-left: 7.5%;"> 
    <img src="Img/S5- LACE.jpg" style="width: 100%;"> 
    <h3> S5 - Lace </h3>
    <p class="price"> 22cm - ₱1,500 per yard  </p>
   </div> 

   <div class="card" style="margin-left: 0%;"> 
    <img src="Img/S6-LACE.jpg" style="width: 100%;"> 
    <h3>  S6 - Lace </h3>
    <p class="price"> 22cm - ₱1,500 per yard </p>
   </div>

   <div class="card" style="margin-left: 0%;"> 
    <img src="Img/S7-LACE.jpg" style="width: 100%;"> 
    <h3> S7 - Lace </h3>
    <p class="price"> 22cm - ₱1,500 per yard </p>
   </div> 

</div>
<!--END-->
</br>
</br> 


<!--FOOTER-->
<footer>
  <div class="container">
      <div class="footer-left">
          <h3>Location</h3>
          <p>Blk 2 Lot 15,<br>Annex 3 Shappel Homes Perpetual Village 8<br>Habay 1 , Bacoor Cavite, Philippines</p>
      </div>
      <div class="footer-right">
          <h3>Opening Hours</h3>
          <ul>
              <li>Monday - Friday: 10am - 10pm</li>
              <li>Saturday: 11am - 11pm</li>
              <li>Sunday: Closed</li>
          </ul>
          <div class="social">
              <a href="https://www.facebook.com/LvmenLiturgicalVestments" target="_blank" ><img src="Img/facebook.png" alt="Facebook"></a>
              <a href="#"><img src="Img/twitter.png" target="_blank" alt="Twitter"></a>
              <a href="https://www.instagram.com/explore/locations/108212715189138/dankatsu/" target="_blank" ><img src="Img/instagram.png" alt="Instagram"></a>
          </div>
      </div>
  </div>
</footer>
<!--END-->


</body>
</html>
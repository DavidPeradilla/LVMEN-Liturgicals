<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $conn = new mysqli("localhost", "root", "", "shopping_cart");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $sql = "UPDATE slideshow_images SET active = NOT active WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: slideshow.php"); 
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

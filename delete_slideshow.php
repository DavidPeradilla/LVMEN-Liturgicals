<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $conn = new mysqli("localhost", "root", "", "shopping_cart");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

  
    $sql = "SELECT image_path FROM slideshow_images WHERE id = $id";
    $result = $conn->query($sql);
    $image = $result->fetch_assoc();

    if (file_exists($image['image_path'])) {
        unlink($image['image_path']);
    }

  
    $sql = "DELETE FROM slideshow_images WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: slideshow.php"); 
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $conn = new mysqli("localhost", "root", "", "shopping_cart");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the image path
    $sql = "SELECT image_path FROM slideshow_images WHERE id = $id";
    $result = $conn->query($sql);
    $image = $result->fetch_assoc();

    // Delete the image from the filesystem
    if (file_exists($image['image_path'])) {
        unlink($image['image_path']);
    }

    // Delete the image from the database
    $sql = "DELETE FROM slideshow_images WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: content_manager.php"); // Redirect back to dashboard
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

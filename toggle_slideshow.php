<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $conn = new mysqli("localhost", "root", "", "shopping_cart");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Toggle the active status
    $sql = "UPDATE slideshow_images SET active = NOT active WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php"); // Redirect back to dashboard
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

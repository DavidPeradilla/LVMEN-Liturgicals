<?php
if (isset($_POST['submit_image'])) {
    $target_dir = "Img/slideshow/";
    $target_file = $target_dir . basename($_FILES["slideshow_image"]["name"]);
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is an image
    if (getimagesize($_FILES["slideshow_image"]["tmp_name"])) {
        // Check if the file already exists
        if (!file_exists($target_file)) {
            // Check file size (optional)
            if ($_FILES["slideshow_image"]["size"] < 5000000) { // Max 5MB
                // Move the uploaded file to the correct directory
                if (move_uploaded_file($_FILES["slideshow_image"]["tmp_name"], $target_file)) {
                    // Store the image path in the database
                    $conn = new mysqli("localhost", "root", "", "shopping_cart");
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "INSERT INTO slideshow_images (image_path) VALUES ('$target_file')";
                    if ($conn->query($sql) === TRUE) {
                        echo "New image uploaded successfully.";
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            } else {
                echo "Sorry, your file is too large.";
            }
        } else {
            echo "Sorry, file already exists.";
        }
    } else {
        echo "Sorry, only image files are allowed.";
    }
}
?>

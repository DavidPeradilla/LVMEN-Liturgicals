<?php
session_start();
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all slideshow images for management
$sql = "SELECT * FROM slideshow_images";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Slideshow</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <h1>Admin Dashboard - Slideshow Management</h1>

    <!-- Upload new slideshow image -->
    <h2>Upload New Slideshow Image</h2>
    <form action="upload_slideshow.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="slideshow_image" accept="image/*" required>
        <button type="submit" name="submit_image">Upload Image</button>
    </form>

    <!-- Manage Existing Slideshow Images -->
    <h3>Manage Slideshow Images</h3>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($image = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($image['image_path']); ?>" width="100"></td>
                    <td><?php echo $image['active'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <a href="delete_slideshow.php?id=<?php echo $image['id']; ?>" onclick="return confirm('Are you sure you want to delete this image?')">Delete</a> |
                        <a href="toggle_slideshow.php?id=<?php echo $image['id']; ?>"><?php echo $image['active'] ? 'Deactivate' : 'Activate'; ?></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
session_name("admin_session");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM slideshow_images";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Slideshow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="admin_sales.php"><i class="fas fa-tachometer-alt"></i><span>Overview</span></a>
        <a href="content_manager2.php"><i class="fas fa-cogs"></i><span>Content Manager</span></a>
        <a href="upload.php"><i class="fas fa-upload"></i><span>Manage Products</span></a>
        <a href="show_users2.php"><i class="fas fa-users"></i><span>Manage Users</span></a>
        <a href="admin_orders.php"><i class="fas fa-box"></i><span>Manage Orders</span></a>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i><span>Check Sales</span></a>
        <a href="logout_admin.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 py-8">
    
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Upload New Slideshow Image</h2>
    <form action="upload_slideshow.php" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
        <input type="file" name="slideshow_image" accept="image/*" required class="mb-4 block w-full text-sm text-gray-700 border border-gray-300  cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit" name="submit_image" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">Upload Image</button>
    </form>

    
    <h3 class="text-xl font-semibold mt-10 mb-4 text-gray-700">Manage Slideshow Images</h3>
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-200 text-gray-800 text-left">
                <tr>
                    <th class="px-6 py-3">Image</th>
                    <th class="px-6 py-3">Active</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($image = $result->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" width="100" class="rounded shadow">
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-white <?php echo $image['active'] ? 'bg-green-500' : 'bg-red-500'; ?>">
                                <?php echo $image['active'] ? 'Yes' : 'No'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 space-x-4">
                            <a href="delete_slideshow.php?id=<?php echo $image['id']; ?>"
                               onclick="return confirm('Are you sure you want to delete this image?')"
                               class="text-red-600 hover:underline">Delete</a>
                            <a href="toggle_slideshow.php?id=<?php echo $image['id']; ?>"
                               class="text-blue-600 hover:underline">
                               <?php echo $image['active'] ? 'Deactivate' : 'Activate'; ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

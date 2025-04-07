<?php
$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM featured_products WHERE id = $id");
$product = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    
    // Update database
    $stmt = $conn->prepare("UPDATE featured_products SET name = ?, price = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $price, $id);
    $stmt->execute();
    
    header("Location: admin_featured.php");
}
?>

<form method="POST">
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>
    <input type="text" name="price" value="<?= htmlspecialchars($product['price']); ?>" required>
    <button type="submit">Update Product</button>
</form>


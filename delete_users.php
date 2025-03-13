<?php
include('db.php'); // Include database connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete user query
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href='edit_users.php';</script>";
    } else {
        echo "<script>alert('Error deleting user!'); window.location.href='delete_users.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>

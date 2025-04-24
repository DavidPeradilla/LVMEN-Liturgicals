<?php
session_name("user_session");
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$email = $_SESSION['email'];

$conn = new mysqli("localhost", "root", "", "shopping_cart");

if ($conn->connect_error) {
    echo json_encode(['count' => 0]);
    exit;
}

$sql = "SELECT SUM(quantity) as total FROM cart WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$count = $row['total'] ?? 0;

echo json_encode(['count' => (int)$count]);

$stmt->close();
$conn->close();

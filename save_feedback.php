<?php
header('Content-Type: application/json'); // <-- This is essential!

$host = "localhost";
$user = "root";
$password = "";
$database = "recommendation";

// Get feedback data from POST request
$data = json_decode(file_get_contents("php://input"), true);
$recommendation_name = $data['recommendation_name'];
$rating = $data['rating'];

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

// SQL query to insert feedback into database
$sql = "INSERT INTO feedback (recommendation_name, rating) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $recommendation_name, $rating);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>

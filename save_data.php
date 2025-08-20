<?php
session_start(); 
$host = "localhost";
$user = "root";
$password = "";
$dbname = "recommendation";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$user_email = $_SESSION['email'] ?? null;
if (!$user_email) {
    echo "User not logged in.";
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$recommendationType = $conn->real_escape_string($data['recommendationType'] ?? '');
// $gender = $conn->real_escape_string($data['gender'] ?? '');
$age = (int)($data['age'] ?? 0);
$symptoms = $conn->real_escape_string($data['symptoms'] ?? '');
$disease = $conn->real_escape_string($data['disease'] ?? '');
$recommendation = $conn->real_escape_string($data['recommendation'] ?? '');

$sql = "INSERT INTO treatment_recommendations 
        (user_email, recommendation_type, age, symptoms, disease, recommendation)
        VALUES ('$user_email', '$recommendationType', $age, '$symptoms', '$disease', '$recommendation')";

if ($conn->query($sql)) {
    echo "Data saved successfully";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();

?>

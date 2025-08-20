<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "recommendation";

// Check if user is logged in via email
if (!isset($_SESSION['email'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed");
}

$email = $_SESSION['email']; // Get logged-in user's email

// Use prepared statement to fetch recommendations for the user
$stmt = $conn->prepare("SELECT DISTINCT recommendation FROM treatment_recommendations WHERE user_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$recommendations = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recommendations[] = htmlspecialchars($row['recommendation']);
    }
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($recommendations);
?>

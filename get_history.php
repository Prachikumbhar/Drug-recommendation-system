<?php
session_start();
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";
$dbname = "recommendation";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$user_email = $_SESSION['email'] ?? null;
if (!$user_email) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$sql = "SELECT * FROM treatment_recommendations 
        WHERE user_email = ? 
        ORDER BY created_at DESC LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$history = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
}

echo json_encode($history);

$conn->close();
?>

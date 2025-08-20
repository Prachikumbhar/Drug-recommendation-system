<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "recommendation";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Please fill in both email and password.";
        header("Location: newlog.html");
        exit;
    }

    $stmt = $conn->prepare("SELECT id, name, email, pass FROM register WHERE LOWER(email) = LOWER(?) LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['pass'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['email'] = $row['email'];
            header("Location: main1.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Invalid email or password.";
            header("Location: newlog.html");
            exit;
        }
    } else {
        $_SESSION['error_message'] = "User not found.";
        header("Location: newlog.html");
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>
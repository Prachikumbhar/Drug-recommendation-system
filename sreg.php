<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recommendation";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $terms = isset($_POST['terms']) ? 1 : 0;

    if (!empty($name) && !empty($email) && !empty($password) && !empty($confirm_password) && $terms) {
        if ($password !== $confirm_password) {
            echo '<script>alert("Passwords do not match!"); window.location.href="sreg.html";</script>';
            exit();
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM register WHERE email = ?");
        if ($stmt === false) {
            die("Error preparing SELECT query: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo '<script>alert("Email already registered!"); window.location.href="sreg.html";</script>';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO register (name, email, pass, cpass, terms) VALUES (?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die("Error preparing INSERT query: " . $conn->error);
            }

            $stmt->bind_param("ssssi", $name, $email, $hashedPassword, $hashedPassword, $terms);

            if ($stmt->execute()) {
                echo '<script>alert("Registration successful! Redirecting to login..."); window.location.href = "newlog.html";</script>';
                exit();
            } else {
                echo '<script>alert("Registration failed. Try again!"); window.location.href="sreg.html";</script>';
            }
        }
        $stmt->close();
    } else {
        echo '<script>alert("Please fill in all fields and accept the terms."); window.location.href="sreg.html";</script>';
    }
}

$conn->close();
?>

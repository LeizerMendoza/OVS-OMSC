<?php
ob_start(); // Optional: buffer output to prevent "headers already sent"

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection (make sure the path is correct)
include __DIR__ . '/../database.php';

// --- Check if user is logged in ---
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("Not logged in");
}

// --- Fetch user data ---
$stmt = $conn->prepare("SELECT fullname, student_id, email FROM users WHERE id = ?");
if (!$stmt) die("SQL Error: " . $conn->error);

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if (!$userData) die("User not found");

$fullname   = $userData['fullname'];
$student_id = $userData['student_id'];
$email      = $userData['email'];

// End output buffering (optional)
ob_end_flush();
?>


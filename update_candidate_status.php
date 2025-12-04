<?php
include '../database.php';

$student_id = $_POST['student_id'] ?? '';
$status = $_POST['status'] ?? '';

if ($student_id && $status) {
    $stmt = $conn->prepare("UPDATE candidates SET status = ? WHERE student_id = ?");
    $stmt->bind_param("ss", $status, $student_id);
    $stmt->execute();
    echo "OK";
} else {
    echo "Invalid request";
}
?>

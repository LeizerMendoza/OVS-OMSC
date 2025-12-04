<?php
session_start();
include '../database.php';
header('Content-Type: application/json');

// 1️⃣ Get raw input
$input = file_get_contents('php://input');

// 🔹 DEBUG: log raw input
file_put_contents('debug.log', date('Y-m-d H:i:s') . " - RAW INPUT: " . $input . "\n", FILE_APPEND);

// 2️⃣ Check if input is empty
if (empty($input)) {
    echo json_encode(['success'=>false,'message'=>'No JSON received']);
    exit;
}

// 3️⃣ Decode JSON
$data = json_decode($input, true);

// 4️⃣ Validate data
if (!isset($data['student_id']) || !isset($data['status'])) {
    echo json_encode(['success'=>false,'message'=>'Missing or invalid data']);
    exit;
}

// 5️⃣ Extract values
$student_id = $data['student_id'];
$status = $data['status'];

// 6️⃣ Prepare & execute query
$stmt = $conn->prepare("UPDATE candidates SET status=? WHERE student_id=?");
$stmt->bind_param("si", $status, $student_id);

if($stmt->execute()){
    echo json_encode(['success'=>true,'message'=>'Status updated successfully']);
} else {
    echo json_encode(['success'=>false,'message'=>'Failed to update status']);
}

$stmt->close();
$conn->close();
?>

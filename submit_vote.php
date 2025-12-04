<?php
session_start();
include '../database.php';

// --- 1. Get logged-in user ID ---
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("❌ You must be logged in to vote.");
}

// --- 2. Check if user has already voted ---
$stmt = $conn->prepare("SELECT has_voted FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$hasVoted = $result->fetch_assoc()['has_voted'] ?? 0;
$stmt->close();

if ($hasVoted):
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Already Voted</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
<div class="bg-white p-6 rounded-2xl shadow-lg max-w-sm text-center animate-fadeIn">
    <div class="text-yellow-500 text-4xl mb-3">
      <i class="fas fa-exclamation-triangle"></i>
    </div>
    <h1 class="text-xl font-semibold mb-2 text-gray-800">❌ Already Voted</h1>
    <p class="text-gray-600 mb-4 text-sm">
        You have already submitted your votes. Multiple submissions are not allowed.
    </p>
    <a href="../student/welcome_home.php" 
       class="inline-block px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg shadow hover:bg-gray-900 transition">
        Go Back Home
    </a>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(-20px);} to { opacity:1; transform:translateY(0);} }
.animate-fadeIn { animation: fadeIn 0.6s ease-out; }
</style>
</body>
</html>
<?php
exit;
endif;

// --- 3. Validate submitted votes ---
if (empty($_POST['vote']) || !is_array($_POST['vote'])) {
    die("❌ No votes submitted.");
}

try {
    // Start transaction
    $conn->begin_transaction();

    foreach ($_POST['vote'] as $electionType => $positions) {
        foreach ($positions as $position => $candidateIds) {

            if (is_array($candidateIds)) {
                if (count($candidateIds) < 1) {
                    throw new Exception("❌ You must select at least 1 candidate for $position in $electionType.");
                }
                foreach ($candidateIds as $cid) {
                    $stmt = $conn->prepare("INSERT INTO votes (student_id, candidate_id, date_voted) VALUES (?, ?, NOW())");
                    if (!$stmt) throw new Exception("SQL Error: " . $conn->error);
                    $stmt->bind_param("ii", $user_id, $cid);
                    $stmt->execute();
                    $stmt->close();
                }
            } else {
                if (empty($candidateIds)) {
                    throw new Exception("❌ You must select a candidate for $position in $electionType.");
                }
                $stmt = $conn->prepare("INSERT INTO votes (student_id, candidate_id, date_voted) VALUES (?, ?, NOW())");
                if (!$stmt) throw new Exception("SQL Error: " . $conn->error);
                $stmt->bind_param("ii", $user_id, $candidateIds);
                $stmt->execute();
                $stmt->close();
            }

        }
    }

    // --- Mark user as voted ---
    $stmt = $conn->prepare("UPDATE users SET has_voted = 1 WHERE id = ?");
    if (!$stmt) throw new Exception("SQL Error: " . $conn->error);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    die("❌ Error submitting vote: " . $e->getMessage());
}

// --- 4. Show success notification ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Vote Submitted</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-green-50 via-green-100 to-green-200">
 <div class="bg-white p-6 rounded-2xl shadow-lg max-w-sm text-center animate-fadeIn">
    <div class="text-green-500 text-4xl mb-3">
        <i class="fas fa-check-circle"></i>
    </div>
    <h1 class="text-xl font-semibold mb-2 text-green-700">✅ Vote Submitted</h1>
    <p class="text-gray-600 mb-4 text-sm">
        Your votes have been successfully recorded. Thank you for participating!
    </p>
    <div class="flex flex-col gap-3">
    <a href="../student/review_vote.php" 
       class="inline-block px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition text-center">
        Review Vote
    </a>
    <a href="../student/welcome_home.php" 
       class="inline-block px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg shadow hover:bg-green-700 transition text-center">
        Back to Home
    </a>
</div>

</div>

</body>
</html>

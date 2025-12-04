<?php
session_start();
include '../database.php';

$selected_type = isset($_GET['type']) ? $_GET['type'] : '';

if (!$selected_type) {
    die("Election type not selected.");
}

// Fetch all unique positions for the selected election type
$positions = [];
$stmt = $conn->prepare("SELECT DISTINCT position FROM candidates WHERE election_type = ?");
$stmt->bind_param("s", $selected_type);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $positions[] = $row['position'];
}
$stmt->close();

// Get top (winner) and bottom (loser) candidate per position
$results = [];
foreach ($positions as $pos) {
    $stmt = $conn->prepare("SELECT name, partylist, votes FROM candidates WHERE election_type = ? AND position = ? ORDER BY votes DESC");
    $stmt->bind_param("ss", $selected_type, $pos);
    $stmt->execute();
    $res = $stmt->get_result();

    $candidates = [];
    while ($row = $res->fetch_assoc()) {
        $candidates[] = $row;
    }

    if (count($candidates) > 0) {
        $results[$pos]['winner'] = $candidates[0];
        $results[$pos]['loser']  = $candidates[count($candidates) - 1];
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Election Outcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">

    <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg p-6 space-y-6">

        <h1 class="text-2xl font-bold text-center text-indigo-700">
            Election Outcome for <?= htmlspecialchars($selected_type) ?>
        </h1>

        <?php foreach ($results as $position => $info): ?>
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($position) ?></h2>
                <div class="mt-2 grid md:grid-cols-2 gap-4">
                    
                    <!-- Winner Card -->
                    <div class="bg-green-100 p-4 rounded-lg shadow">
                        <h3 class="font-bold text-green-700">Winner</h3>
                        <p>
                            <strong><?= htmlspecialchars($info['winner']['name']) ?></strong>
                            (<?= htmlspecialchars($info['winner']['partylist']) ?>)
                        </p>
                        <p>Votes: <?= (int)$info['winner']['votes'] ?></p>
                    </div>

                    <!-- Loser Card -->
                    <div class="bg-red-100 p-4 rounded-lg shadow">
                        <h3 class="font-bold text-red-700">Loser</h3>
                        <p>
                            <strong><?= htmlspecialchars($info['loser']['name']) ?></strong>
                            (<?= htmlspecialchars($info['loser']['partylist']) ?>)
                        </p>
                        <p>Votes: <?= (int)$info['loser']['votes'] ?></p>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>

        <!-- Back Link -->
        <div class="text-center mt-6">
            <a href="../admin/election_results.php?type=<?= urlencode($selected_type) ?>"
               class="text-indigo-600 hover:underline">
                ← Back to Results
            </a>
        </div>

    </div>

</body>
</html>

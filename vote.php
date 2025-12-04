<?php
session_start();
include '../database.php';


// Get candidates
$query = "SELECT * FROM candidates WHERE election_type = ? 
ORDER BY partylist, 
FIELD(position, 'Governor', 'Vice Governor', 
'Board Member 1', 'Board Member 2', 'Board Member 3', 'Board Member 4', 
'Board Member 5', 'Board Member 6', 'Board Member 7', 'Board Member 8', 'Board Member 9')";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $election_type);
$stmt->execute();
$result = $stmt->get_result();

$candidates_by_partylist = [];
while ($row = $result->fetch_assoc()) {
    $partylist = $row['partylist'] ?: 'Independent';
    $candidates_by_partylist[$partylist][] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vote - <?= htmlspecialchars($election_type) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 p-6">
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow">
        <h1 class="text-3xl font-bold text-center text-blue-700 mb-10">
            🗳️ Voting for <?= htmlspecialchars($election_type) ?> Election
        </h1>
        <form action="../student/submit_vote.php" method="POST">
            <input type="hidden" name="election_type" value="<?= htmlspecialchars($election_type) ?>">
            <div class="grid grid-cols-1 md:grid-cols-<?= count($candidates_by_partylist) ?> gap-6">
                <?php foreach ($candidates_by_partylist as $partylist => $candidates): ?>
                    <div class="bg-gray-100 p-4 rounded-lg border border-gray-300">
                        <h2 class="text-xl font-semibold text-center text-purple-700 mb-4">
                            <?= htmlspecialchars($partylist) ?>
                        </h2>
                        <?php foreach ($candidates as $candidate): ?>
                            <label class="flex items-center bg-white border rounded-md px-3 py-2 mb-3 shadow hover:bg-blue-100">
                                <input type="radio" name="vote_<?= htmlspecialchars($candidate['position']) ?>" value="<?= $candidate['id'] ?>" class="mr-2">
                                <div>
                                    <span class="font-medium"><?= htmlspecialchars($candidate['name']) ?></span>
                                    <div class="text-sm text-gray-600"><?= htmlspecialchars($candidate['position']) ?></div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-10">
                <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700">✅ Submit Vote</button>
            </div>
        </form>
    </div>
    <a href="../student/election_type.php" 
   class="no-print fixed bottom-6 left-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6 rounded-full shadow-2xl hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 font-semibold flex items-center space-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
        <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6z"/>
    </svg>
    <span>Back</span>
</a>

</body>
</html>

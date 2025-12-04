<?php 
session_start();
include '../database.php';

$selected_type = isset($_GET['type']) ? $_GET['type'] : '';

$candidates = [];
if ($selected_type) {
    $stmt = $conn->prepare("SELECT name, partylist, position, votes FROM candidates WHERE election_type = ? ORDER BY partylist, votes DESC");
    $stmt->bind_param("s", $selected_type);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Election Results - Printable</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white p-8">
  <!-- Header -->
  <header class="flex flex-col md:flex-row items-center justify-between border-b-4 border-indigo-600 pb-4 mb-8">
    <div class="flex items-center space-x-4">
      <img src="../omsc-logo.png" alt="Logo" class="w-20 h-20">
      <div>
        <h1 class="text-2xl font-extrabold">Occidental Mindoro State College</h1>
        <p class="text-lg">Mamburao Campus</p>
        <p class="text-sm text-gray-600">Official Election Results</p>
      </div>
    </div>
    <div class="text-right mt-4 md:mt-0">
      <p class="text-sm text-gray-700">Date: <strong><?= date('F d, Y') ?></strong></p>
      <p class="text-sm text-gray-700">Type: <strong><?= htmlspecialchars($selected_type) ?></strong></p>
    </div>
  </header>

    <?php if (!empty($candidates)) { 

        $party_lists = [];
        foreach ($candidates as $candidate) {
            $party_lists[$candidate['partylist']][] = $candidate;
        }
    ?>
        <?php foreach ($party_lists as $partylist => $party_candidates) { ?>
            <div class="mb-8">
                <h2 class="text-xl font-bold text-indigo-700 mb-2"><?= htmlspecialchars($partylist) ?></h2>
                <table class="w-full table-auto border border-gray-300 mb-4">
                    <thead>
                        <tr class="bg-indigo-600 text-white">
                            <th class="px-4 py-2 border border-gray-300">Candidate Name</th>
                            <th class="px-4 py-2 border border-gray-300">Position</th>
                            <th class="px-4 py-2 border border-gray-300 text-center">Votes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($party_candidates as $candidate) { ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($candidate['name']) ?></td>
                                <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($candidate['position']) ?></td>
                                <td class="px-4 py-2 border border-gray-300 text-center font-semibold"><?= $candidate['votes'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

        <div class="mt-12 flex justify-between">
            <div>
                <p class="font-bold underline">Prepared by:</p>
                <p class="mt-8">_____________________________</p>
                <p class="text-sm text-gray-600">Election Committee Head</p>
            </div>
            <div>
                <p class="font-bold underline">Certified by:</p>
                <p class="mt-8">_____________________________</p>
                <p class="text-sm text-gray-600">Campus Director</p>
            </div>
        </div>

    <?php } else { ?>
        <p class="text-center text-red-600">⚠️ No results available for this election type.</p>
    <?php } ?>

    <div class="mt-8 no-print flex justify-center">
    <button onclick="window.print()" 
        class="bg-white border-2 border-green-600 text-green-700 font-semibold py-2 px-6 rounded-lg hover:bg-green-600 hover:text-white transition">
        🖨️ Print Report
    </button>
</div>

    <a href="../admin/election_results.php" 
   class="no-print fixed bottom-6 left-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6 rounded-full shadow-2xl hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 font-semibold flex items-center space-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
        <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6z"/>
    </svg>
    <span>Back</span>
</a>

<div id="printNotification" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 hidden z-50">
    <div class="bg-white p-8 rounded-xl shadow-2xl border-2 border-indigo-600 max-w-md w-full text-center">
        <div class="flex justify-center mb-4">
            <img src="../omsc-logo.png" alt="OMSC Logo" class="w-16 h-16">
        </div>
        <h2 class="text-2xl font-bold text-indigo-700 mb-2">Printing Completed</h2>
        <p class="text-gray-600 text-sm mb-6">You have successfully printed the election results. Thank you!</p>
        <button onclick="closeNotification()" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2 px-6 rounded-full transition duration-200">
            OK
        </button>
    </div>
</div>

<script>
function showNotification() {
    document.getElementById('printNotification').classList.remove('hidden');
}

function closeNotification() {
    document.getElementById('printNotification').classList.add('hidden');
}

window.addEventListener('afterprint', (event) => {
    showNotification();
});
</script>


</body>
</html>

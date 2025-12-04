<?php 
session_start();
include '../database.php';

$selected_type = isset($_GET['type']) ? $_GET['type'] : '';

$positions = [];
$results = [];

if ($selected_type) {
    $stmt = $conn->prepare("SELECT DISTINCT position FROM candidates WHERE election_type = ?");
    $stmt->bind_param("s", $selected_type);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $positions[] = $row['position'];
    }
    $stmt->close();

    foreach ($positions as $position) {
        $stmt = $conn->prepare("SELECT name, partylist, votes FROM candidates WHERE election_type = ? AND position = ? ORDER BY votes DESC");
        $stmt->bind_param("ss", $selected_type, $position);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $results[$position][] = $row;
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Formal Election Report</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { font-size: 0.875rem; } /* Tailwind text-sm = ~14px */
        @media print {
            .no-print { display: none; }
            body { margin: 0; font-size: 0.75rem; } /* Smaller print font */
            table, th, td { page-break-inside: avoid !important; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 px-8 py-6 font-serif">

<!-- Header -->
<header class="border-b-2 border-black pb-4 mb-6 flex justify-between items-center">
    <div class="flex items-center space-x-4">
        <img src="../omsc-logo.png" alt="OMSC Logo" class="w-16 h-16">
        <div>
            <h1 class="text-lg font-bold">Occidental Mindoro State College</h1>
            <p class="text-sm">Mamburao Campus</p>
            <p class="text-xs italic">Student Election - Official Results Report</p>
        </div>
    </div>
    <div class="text-right text-sm">
        <p><strong>Date:</strong> <?= date('F d, Y') ?></p>
        <p><strong>Election Type:</strong> <?= htmlspecialchars($selected_type) ?></p>
    </div>
</header>

<?php if (!empty($results)): ?>
    <section class="mb-8">
        <h2 class="text-base font-semibold text-indigo-800 mb-3">Summary of Election Results</h2>
        <table class="w-full table-fixed border border-gray-400 text-xs">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="w-1/4 px-2 py-1 border border-gray-300 text-left">Position</th>
                    <th class="w-1/4 px-2 py-1 border border-gray-300 text-left">Candidate Name</th>
                    <th class="w-1/4 px-2 py-1 border border-gray-300 text-left">Partylist</th>
                    <th class="w-1/8 px-2 py-1 border border-gray-300 text-center">Votes</th>
                    <th class="w-1/8 px-2 py-1 border border-gray-300 text-center">Result</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $position => $candidates): ?>
                    <?php foreach ($candidates as $index => $cand): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="px-2 py-1 border border-gray-300"><?= htmlspecialchars($position) ?></td>
                            <td class="px-2 py-1 border border-gray-300"><?= htmlspecialchars($cand['name']) ?></td>
                            <td class="px-2 py-1 border border-gray-300"><?= htmlspecialchars($cand['partylist']) ?></td>
                            <td class="px-2 py-1 border border-gray-300 text-center"><?= (int)$cand['votes'] ?></td>
                            <td class="px-2 py-1 border border-gray-300 text-center font-semibold">
                                <?php if ($index === 0): ?>
                                    <span class="text-green-700">Win</span>
                                <?php elseif ($index === count($candidates) - 1): ?>
                                    <span class="text-red-700">Lose</span>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- Signatures -->
    <section class="mt-12 flex justify-between text-sm">
        <div class="text-left">
            <p class="font-semibold underline">Prepared by:</p>
            <div class="mt-10">___________________________</div>
            <p class="text-xs text-gray-700 mt-1">Election Committee Chairperson</p>
        </div>
        <div class="text-right">
            <p class="font-semibold underline">Certified correct by:</p>
            <div class="mt-10">___________________________</div>
            <p class="text-xs text-gray-700 mt-1">Campus Director</p>
        </div>
    </section>
<?php else: ?>
    <p class="text-center text-red-600 text-sm mt-10">⚠️ No election data available for this type.</p>
<?php endif; ?>

<!-- Buttons -->
<div class="no-print mt-10 flex justify-center gap-4">
    <button onclick="window.print()"  class="bg-white border-2 border-green-600 text-green-700 font-semibold py-2 px-6 rounded-lg hover:bg-green-600 hover:text-white transition">
        🖨️ Print Report
    </button>
    

<!-- Print Notification -->
<div id="printNotification" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 hidden z-50">
    <div class="bg-white p-8 rounded-xl shadow-2xl border-2 border-indigo-600 max-w-md w-full text-center">
        <div class="flex justify-center mb-4">
            <img src="../omsc-logo.png" alt="OMSC Logo" class="w-16 h-16">
        </div>
        <h2 id="notificationTitle" class="text-xl font-bold text-indigo-700 mb-2">Print Complete</h2>
        <p id="notificationMessage" class="text-gray-600 text-sm mb-6">The report has been successfully printed. Thank you.</p>
        <button onclick="closeNotification()" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2 px-6 rounded-full transition duration-200">
            OK
        </button>
    </div>
</div>

<script>
let printCanceled = false;

function showNotification() {
    document.getElementById('printNotification').classList.remove('hidden');
}

function closeNotification() {
    document.getElementById('printNotification').classList.add('hidden');
}

function startPrint() {
    printCanceled = false;
    window.print();
}

window.onbeforeprint = function() {
    printCanceled = false;
};

window.onafterprint = function() {
    if (!printCanceled) {
        showNotification();
    }
};
</script>

 
    <a href="../admin/election_results.php" 
    class="no-print fixed bottom-6 left-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6 rounded-full shadow-2xl hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 font-semibold flex items-center space-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
        <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6z"/>
    </svg>
    <span>Back</span>
</a>
</body>
</html>

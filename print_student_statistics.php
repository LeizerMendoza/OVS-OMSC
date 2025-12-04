<?php
session_start();
include 'database.php';

$query = "SELECT * FROM students_data WHERE id = 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row) {
    $total_students = $row['total_students'];
    $beed_students = $row['beed_students'];
    $bsit_students = $row['bsit_students'];
    $cbam_students = $row['cbam_students'];
    $beed_voted = $row['beed_voted'];
    $bsit_voted = $row['bsit_voted'];
    $cbam_voted = $row['cbam_voted'];
    $beed_not_voted = $row['beed_not_voted'];
    $bsit_not_voted = $row['bsit_not_voted'];
    $cbam_not_voted = $row['cbam_not_voted'];
} else {
    // Fallback static data
    $total_students = 4231;
    $beed_students = 997;
    $bsit_students = 1240;
    $cbam_students = 1994;
    $beed_voted = 748;
    $bsit_voted = 956;
    $cbam_voted = 1467;
    $beed_not_voted = $beed_students - $beed_voted;
    $bsit_not_voted = $bsit_students - $bsit_voted;
    $cbam_not_voted = $cbam_students - $cbam_voted;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Student Voting Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white p-8 text-gray-900 font-serif">

<!-- Header -->
<div class="flex items-center justify-between border-b-2 pb-4 mb-8">
    <div class="flex items-center space-x-4">
        <img src="omsc-logo.png" alt="OMSC Logo" class="w-16 h-16">
        <div>
            <h1 class="text-2xl font-bold">Occidental Mindoro State College</h1>
            <p class="text-lg">Mamburao Campus</p>
            <p class="text-sm text-gray-600 italic">Official Student Voting Statistics Report</p>
        </div>
    </div>
    <div class="text-right">
        <p class="text-sm text-gray-600"><strong>Date:</strong> <?= date('F d, Y') ?></p>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 text-center">
    <div class="bg-gradient-to-r from-blue-200 to-blue-400 p-6 rounded-xl shadow">
        <h2 class="text-lg font-semibold text-blue-800">Total Number of Students</h2>
        <p class="text-3xl font-bold mt-2"><?= $total_students ?></p>
    </div>
    <div class="bg-gradient-to-r from-green-200 to-green-400 p-6 rounded-xl shadow">
        <h2 class="text-lg font-semibold text-green-800">Total Number of Voters</h2>
        <p class="text-3xl font-bold mt-2"><?= $beed_voted + $bsit_voted + $cbam_voted ?></p>
    </div>
    <div class="bg-gradient-to-r from-red-200 to-red-400 p-6 rounded-xl shadow">
        <h2 class="text-lg font-semibold text-red-800">Total Number of Non-Voters</h2>
        <p class="text-3xl font-bold mt-2"><?= $beed_not_voted + $bsit_not_voted + $cbam_not_voted ?></p>
    </div>
</div>

<!-- Breakdown Table -->
<div class="overflow-x-auto mt-10">
    <h2 class="text-2xl font-bold text-center text-indigo-700 mb-6">Breakdown of Voter Participation</h2>
    <table class="w-full table-auto border-collapse border border-gray-300 mb-8 text-sm">
        <thead class="bg-indigo-600 text-white">
            <tr>
                <th class="px-6 py-3 border-b border-gray-300">Program</th>
                <th class="px-6 py-3 border-b border-gray-300">Total Enrolled</th>
                <th class="px-6 py-3 border-b border-gray-300">Voted</th>
                <th class="px-6 py-3 border-b border-gray-300">Did Not Vote</th>
            </tr>
        </thead>
        <tbody class="bg-gray-50 text-center">
            <tr class="border-b hover:bg-gray-100">
                <td class="py-4 border border-gray-300 font-medium">Bachelor of Elementary Education (BEED)</td>
                <td class="border border-gray-300"><?= $beed_students ?></td>
                <td class="border border-gray-300"><?= $beed_voted ?></td>
                <td class="border border-gray-300"><?= $beed_not_voted ?></td>
            </tr>
            <tr class="border-b hover:bg-gray-100">
                <td class="py-4 border border-gray-300 font-medium">Bachelor of Science in Information Technology (BSIT)</td>
                <td class="border border-gray-300"><?= $bsit_students ?></td>
                <td class="border border-gray-300"><?= $bsit_voted ?></td>
                <td class="border border-gray-300"><?= $bsit_not_voted ?></td>
            </tr>
            <tr class="hover:bg-gray-100">
                <td class="py-4 border border-gray-300 font-medium">College of Business, Accountancy and Management (CBAM)</td>
                <td class="border border-gray-300"><?= $cbam_students ?></td>
                <td class="border border-gray-300"><?= $cbam_voted ?></td>
                <td class="border border-gray-300"><?= $cbam_not_voted ?></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Signature Block -->
<div class="mt-12 flex justify-between text-sm">
    <div>
        <p class="font-bold underline">Prepared by:</p>
        <p class="mt-8">_____________________________</p>
        <p class="text-gray-600">Chairperson, Student Election Committee</p>
    </div>
    <div>
        <p class="font-bold underline">Certified Correct by:</p>
        <p class="mt-8">_____________________________</p>
        <p class="text-gray-600">Campus Director</p>
    </div>
</div>

<!-- Buttons -->
<div class="mt-8 no-print flex justify-center">
    <button onclick="startPrint()" class="bg-white border-2 border-green-600 text-green-700 font-semibold py-2 px-6 rounded-lg hover:bg-green-600 hover:text-white transition">
        🖨️ Print Report
    </button>
</div>

<a href="students_list.php" 
    class="no-print fixed bottom-6 left-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6 rounded-full shadow-2xl hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 font-semibold flex items-center space-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
        <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6z"/>
    </svg>
    <span>Back</span>
</a>

<!-- Print Notification -->
<div id="printNotification" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 hidden z-50">
    <div class="bg-white p-8 rounded-xl shadow-2xl border-2 border-indigo-600 max-w-md w-full text-center">
        <div class="flex justify-center mb-4">
            <img src="omsc-logo.png" alt="OMSC Logo" class="w-16 h-16">
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

</body>
</html>

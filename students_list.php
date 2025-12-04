<?php  
session_start();
include '../database.php'; 


$query = "SELECT * FROM students_data WHERE id = 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
if (!$row) {
    $row = [
        'total_students' => 3956,
        'beed_students' => 997,
        'bsit_students' => 1240,
        'cbam_students' => 1994,
        'beed_voted' => 748,
        'bsit_voted' => 956,
        'cbam_voted' => 1467
    ];
}

$beed_students = isset($row['beed_students']) ? $row['beed_students'] : 997;
$bsit_students = isset($row['bsit_students']) ? $row['bsit_students'] : 1240;
$cbam_students = isset($row['cbam_students']) ? $row['cbam_students'] : 1994;
$beed_voted = isset($row['beed_voted']) ? $row['beed_voted'] : 748;
$bsit_voted = isset($row['bsit_voted']) ? $row['bsit_voted'] : 956;
$cbam_voted = isset($row['cbam_voted']) ? $row['cbam_voted'] :1467;

$beed_not_voted = $beed_students - $beed_voted;
$bsit_not_voted = $bsit_students - $bsit_voted;
$cbam_not_voted = $cbam_students - $cbam_voted;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $beed_students = $_POST['beed_students'];
    $bsit_students = $_POST['bsit_students'];
    $cbam_students = $_POST['cbam_students'];

    $beed_voted = $_POST['beed_voted'];
    $bsit_voted = $_POST['bsit_voted'];
    $cbam_voted = $_POST['cbam_voted'];

    $total_students = $beed_students + $bsit_students + $cbam_students;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Voting Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 min-h-screen flex flex-col items-center py-10 px-4">

<a href="../admin/admin_dashboard.php" 
   class="no-print fixed top-10 left-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6 rounded-full shadow-2xl hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 font-semibold flex items-center space-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
        <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6z"/>
    </svg>
    <span>Back</span>
  </a>

<div class="w-full max-w-6xl bg-white shadow-2xl rounded-2xl p-10">
    
    <!-- Header Section -->
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-indigo-700">Student Voting Data Records</h1>
        <p class="text-gray-500 mt-2">Manage student voting statistics easily.</p>
    </div>

    <!-- Success Message -->
    <?php if (isset($message)) { ?>
        <div class="bg-green-100 p-4 mb-6 rounded-xl text-center text-green-700"><?= $message ?></div>
    <?php } ?>

    <!-- Data Summary Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-indigo-100 p-6 rounded-xl text-center">
            <h2 class="text-lg font-semibold text-indigo-700">Total Students</h2>
            <p class="text-3xl font-bold mt-2"><?= $beed_students + $bsit_students + $cbam_students ?></p>
        </div>
        <div class="bg-green-100 p-6 rounded-xl text-center">
            <h2 class="text-lg font-semibold text-green-700">Total Voted</h2>
            <p class="text-3xl font-bold mt-2"><?= $beed_voted + $bsit_voted + $cbam_voted ?></p>
        </div>
        <div class="bg-red-100 p-6 rounded-xl text-center">
            <h2 class="text-lg font-semibold text-red-700">Total Not Voted</h2>
            <p class="text-3xl font-bold mt-2"><?= $beed_not_voted + $bsit_not_voted + $cbam_not_voted ?></p>
        </div>
    </div>

    <!-- Update Student Statistics Form -->
    <form action="" method="POST" class="space-y-8 bg-white p-8 rounded-2xl shadow-2xl max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-indigo-700 mb-6 text-center">Student Statistics</h2>

        <!-- Input Fields for Each Course -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-gradient-to-r from-blue-200 to-blue-400 p-6 rounded-xl text-center">
                <h2 class="text-lg font-semibold text-blue-700">BEED Students</h2>
                <input type="number" name="beed_students" value="<?= $beed_students ?>" class="w-full p-4 border-2 border-gray-300 rounded-xl mt-4" required>
            </div>
            <div class="bg-gradient-to-r from-purple-200 to-purple-400 p-6 rounded-xl text-center">
                <h2 class="text-lg font-semibold text-purple-700">BSIT Students</h2>
                <input type="number" name="bsit_students" value="<?= $bsit_students ?>" class="w-full p-4 border-2 border-gray-300 rounded-xl mt-4" required>
            </div>
            <div class="bg-gradient-to-r from-yellow-200 to-yellow-400 p-6 rounded-xl text-center">
                <h2 class="text-lg font-semibold text-yellow-700">CBAM Students</h2>
                <input type="number" name="cbam_students" value="<?= $cbam_students ?>" class="w-full p-4 border-2 border-gray-300 rounded-xl mt-4" required>
            </div>
        </div>

        <!-- Input Fields for Voting Data -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="p-6 rounded-xl text-center">
                <h2 class="text-lg font-semibold text-blue-700">BEED Voted</h2>
                <input type="number" name="beed_voted" value="<?= $beed_voted ?>" class="w-full p-4 border-2 border-gray-300 rounded-xl mt-4" required>
            </div>
            <div class="p-6 rounded-xl text-center">
                <h2 class="text-lg font-semibold text-purple-700">BSIT Voted</h2>
                <input type="number" name="bsit_voted" value="<?= $bsit_voted ?>" class="w-full p-4 border-2 border-gray-300 rounded-xl mt-4" required>
            </div>
            <div class="p-6 rounded-xl text-center">
                <h2 class="text-lg font-semibold text-yellow-700">CBAM Voted</h2>
                <input type="number" name="cbam_voted" value="<?= $cbam_voted ?>" class="w-full p-4 border-2 border-gray-300 rounded-xl mt-4" required>
            </div>
        </div>

    <!-- Data Breakdown Table -->
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-center text-indigo-700 mb-6">Breakdown by Course</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-gray-700 text-center">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-6 py-3">Course</th>
                        <th class="px-6 py-3">Total Students</th>
                        <th class="px-6 py-3">Voted</th>
                        <th class="px-6 py-3">Not Voted</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-50">
                    <tr class="border-b">
                        <td class="py-4">BEED</td>
                        <td><?= $beed_students ?></td>
                        <td><?= $beed_voted ?></td>
                        <td><?= $beed_not_voted ?></td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4">BSIT</td>
                        <td><?= $bsit_students ?></td>
                        <td><?= $bsit_voted ?></td>
                        <td><?= $bsit_not_voted ?></td>
                    </tr>
                    <tr>
                        <td class="py-4">CBAM</td>
                        <td><?= $cbam_students ?></td>
                        <td><?= $cbam_voted ?></td>
                        <td><?= $cbam_not_voted ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

    <!-- Print Button -->
 <div class="mt-10 flex justify-center">
        <a href="../admin/print_student_statistics.php" target="_blank"
            class="bg-white border-2 border-green-600 text-green-700 font-semibold py-2 px-6 rounded-lg hover:bg-green-600 hover:text-white transition">
            🖨️ Print
        </a>
    </div>

</body>
</html>

 
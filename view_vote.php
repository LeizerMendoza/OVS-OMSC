<?php
session_start();
include '../database.php';

// --- 1. Get user_id from GET ---
$user_id = $_GET['user_id'] ?? '';
if (empty($user_id)) die("No user selected.");



// --- 2. Fetch student's fullname and student_id for display ---
$userQuery = $conn->prepare("SELECT fullname, student_id FROM users WHERE id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userData = $userResult->fetch_assoc();

if (!$userData) die("User not found.");

$fullname = $userData['fullname'];
$student_id = $userData['student_id']; // Display only

$positionOrder = [
    'SSG' => ['Governor', 'Vice Governor', 'Board Member 1', 'Board Member 2', 'Board Member 3', 'Board Member 4', 'Board Member 5','Board Member 6', 'Board Member 7','Board Member 8', 'Board Member 9'],
    'PADC' => ['Mayor', 'Vice Mayor','Secretary', 'Treasurer','Auditor', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'YMO' => ['Mayor', 'Vice Mayor', 'Secretary', 'Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'YES' => ['Mayor', 'Vice Mayor', 'Secretary', 'Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'LSC' => ['Mayor', 'Vice Mayor', 'Secretary', 'Asst. Secretary','Treasurer','Asst. Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'SPORTS CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer', 'Asst. Treasurer','Auditor', 'PIO', 'Project Manager', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7', 'Councilor 8', 'Councilor 9', 'Councilor 10'],
    'ENGLISH CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer','Auditor', 'PIO', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'SCI-MATH CLUB' => ['President', 'Vice President', 'Secretary', 'Treasurer','Auditor', 'PIO','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'CYMA' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer','Auditor', 'PIO 1', 'PIO 2', 'Project Manager','Muse','Escort'],
    'UMSO' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary','Treasurer','Auditor', 'PIO', 'Project Manager'],
    'SAMFILKO' => ['Pangulo', 'Ikalawang Pangulo', 'Kalihim', 'Ingat Yaman','Taga-Suri', 'Tagapamayapa', 'Tagapangasiwang Proyekto'],
];

// Flatten all positions for FIELD() SQL
$allPositions = [];
foreach ($positionOrder as $positions) {
    $allPositions = array_merge($allPositions, $positions);
}

// Convert to FIELD() SQL format
$orderSQL = "FIELD(c.position, '" . implode("','", $allPositions) . "')";


// --- 3. Fetch votes with candidate details ---
$sql = "SELECT v.id, c.position, c.name AS candidate_name, c.election_type, v.date_voted
        FROM votes v
        JOIN candidates c ON v.candidate_id = c.id
        WHERE v.student_id = ?
        ORDER BY c.election_type, $orderSQL, v.date_voted DESC";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Votes</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50">
  <!-- NAVBAR -->
  <nav class="bg-gradient-to-r from-[#0a2342] via-[#1e3a8a] to-[#3b82f6] fixed top-0 left-0 w-full z-40 shadow-xl backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-20 text-white">
      <a href="../admin/admin_dashboard.html" class="flex items-center space-x-3">
        <img src="../omsc-logo.png" alt="OMSC" class="w-12 h-12 rounded-full border border-white/30 shadow">
        <div>
          <h1 class="font-semibold text-lg">OMSC VoteSphere</h1>
          <p class="text-xs text-blue-100">Student Election Portal - Admin Control Panel</p>
        </div>
      </a>
     <div class="flex items-center gap-4">
        <div class="hidden md:flex items-center space-x-3">
          <div class="text-sm text-white/80">Welcome, Admin</div>
          <button class="bg-white/10 px-3 py-2 rounded-lg hover:bg-white/20 transition"><i class="fas fa-bell"></i></button>
          
        </div>
        <button id="menu-btn" class="md:hidden text-white text-2xl focus:outline-none"><i class="fas fa-bars"></i></button>
      </div>
    </div>
  </nav>

  <!-- LAYOUT -->
  <div class="flex pt-20">

    <!-- SIDEBAR (desktop) -->
<aside id="sidebar" class="w-64 bg-white shadow-md h-screen fixed top-20 left-0 hidden md:block transition-all">
  <div class="p-6 border-b">
    <h2 class="text-[#1e3a8a] font-semibold">Admin Menu</h2>
  </div>
  <ul class="mt-4 text-gray-700">
    <li><a href="../admin/admin_dashboard.html" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-tachometer-alt text-blue-600 w-5"></i><span class="ml-3">Dashboard</span></a></li>
    <li><a href="../admin/manage_candidates.php" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-user-tie text-blue-600 w-5"></i><span class="ml-3">Manage Candidates</span></a></li>
    <li><a href="../admin/manage_voters.php" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-users text-blue-600 w-5"></i><span class="ml-3">Manage Voters</span></a></li>
    <li><a href="../admin/ongoing_elections.html" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-calendar-check text-blue-600 w-5"></i><span class="ml-3">Elections</span></a></li>
    <li><a href="../admin/election_results.php" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-chart-bar text-blue-600 w-5"></i><span class="ml-3">Results</span></a></li>
    <li><a href="../admin/admin_announcements.php" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-bullhorn text-blue-600 w-5"></i><span class="ml-3">Announcements</span></a></li>
    <li><a href="../admin/settings.html" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-cogs text-blue-600 w-5"></i><span class="ml-3">Settings</span></a></li>
    <li><a href="../logout.php" class="flex items-center px-6 py-3 hover:bg-red-50 border-l-4 border-transparent hover:border-red-500 transition"><i class="fas fa-sign-out-alt text-red-500 w-5"></i><span class="ml-3">Logout</span></a></li>
  </ul>
</aside>

<!-- Active Page Highlighter Script -->
<script>
  const currentPage = window.location.pathname.split("/").pop();

  // Loop through all sidebar links
  document.querySelectorAll(".sidebar-link").forEach(link => {
    if (link.getAttribute("href").includes(currentPage)) {
      link.classList.add("bg-blue-100", "border-blue-500");
      link.classList.remove("border-transparent");
    }
  });
</script>
<div class="max-w-4xl mx-auto mt-20 bg-white rounded-xl shadow-lg border border-gray-200 p-8">
    
    <!-- HEADER -->
    <div class="flex items-center justify-between border-b pb-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">CAST BALLOT</h1>
            <p class="text-sm text-gray-500 mt-1">Issued to: 
                <span class="font-semibold text-gray-700"><?= htmlspecialchars($fullname) ?></span>
                (<?= htmlspecialchars($student_id) ?>)
            </p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-500">Generated on:</p>
            <p class="font-medium text-gray-700"><?= date('F d, Y h:i A') ?></p>
        </div>
    </div>

    <!-- TABLE -->
   <?php if ($result->num_rows > 0): ?>
    <?php
        $currentElection = '';
        while ($row = $result->fetch_assoc()):
            if ($currentElection !== $row['election_type']):
                // Print new election type header
                if ($currentElection !== '') echo '</tbody></table><br>';
                
                $currentElection = $row['election_type'];
    ?>
    <!-- ELECTION TYPE HEADER -->
    <h2 class="text-xl font-bold text-gray-800 mb-2 mt-6 border-b pb-2">
        <?= htmlspecialchars($currentElection) ?> Election
    </h2>

    <table class="w-full text-sm border-collapse shadow-sm">
        <thead class="bg-gray-900 text-white text-left">
            <tr>
                <th class="py-3 px-4 rounded-l-lg">Position</th>
                <th class="py-3 px-4">Candidate</th>
                <th class="py-3 px-4 rounded-r-lg">Date Voted</th>
            </tr>
        </thead>
        <tbody class="bg-gray-50 divide-y">
    <?php endif; ?>

            <tr class="hover:bg-gray-100 transition">
                <td class="py-3 px-4 font-medium text-gray-800"><?= htmlspecialchars($row['position']) ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($row['candidate_name']) ?></td>
                <td class="py-3 px-4 text-gray-600"><?= htmlspecialchars($row['date_voted']) ?></td>
            </tr>

    <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="text-gray-500 text-center py-8 italic">No votes recorded for this student.</p>
<?php endif; ?>


    <!-- FOOTER -->
    <div class="mt-8 border-t pt-4 text-center">
        <a href="../admin/manage_voters.php" 
           class="inline-block mt-3 px-5 py-2 bg-gray-800 text-white text-sm rounded-lg shadow hover:bg-black transition">
            &larr; Back to Voters List
        </a>
    </div>
</div>


</body>
</html>

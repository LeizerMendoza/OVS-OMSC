<?php
session_start();
include '../database.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("❌ You must be logged in to view your votes.");
}

// Fetch student's info
$userQuery = $conn->prepare("SELECT fullname, student_id FROM users WHERE id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userData = $userResult->fetch_assoc();
if (!$userData) die("User not found.");

$fullname = $userData['fullname'];
$student_id = $userData['student_id'];

// Fetch votes
$voteSQL = "SELECT c.election_type, c.position, c.name AS candidate_name
            FROM votes v
            JOIN candidates c ON v.candidate_id = c.id
            WHERE v.student_id = ?
            ORDER BY c.election_type, FIELD(c.position, 'Governor','Vice Governor','Board Member 1','Board Member 2','Board Member 3','Board Member 4','Board Member 5','Board Member 6','Board Member 7','Board Member 8','Board Member 9')";
$stmt = $conn->prepare($voteSQL);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$voteResult = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Review My Vote</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<!-- 🌐 Modern App-Style Navigation Bar -->
<nav class="bg-gradient-to-r from-[#0a2342] via-[#1e3a8a] to-[#3b82f6] fixed top-0 left-0 w-full z-50 shadow-xl transition-all duration-500 ease-in-out backdrop-blur-md">
  <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-20 text-white relative">

    <!-- App Branding -->
    <a href="../student/welcome_home.php" class="flex items-center space-x-4">
      <img src="../omsc-logo.png" alt="OMSC Logo" class="w-14 h-14 rounded-full shadow-lg border border-white/40">
      <div class="flex flex-col leading-tight">
        <h1 class="text-lg md:text-xl font-semibold tracking-wide">Occidental Mindoro State College</h1>
        <p class="text-xs md:text-sm text-blue-100 font-medium">OMSC VoteSphere — Student Election Portal</p>
      </div>
    </a>

    <!-- Desktop Navigation -->
    <ul class="hidden md:flex space-x-10 font-medium text-white items-center">
      <li>
        <a href="../student/welcome_home.php" class="flex flex-col items-center group">
          <div class="bg-white/10 p-3 rounded-2xl group-hover:bg-white/20 transition-all duration-300 shadow-md">
            <i class="fas fa-house text-xl"></i>
          </div>
          <span class="text-xs mt-1 group-hover:text-blue-200 transition">Home</span>
        </a>
      </li>

      <li>
        <a href="../student/election_type.php" class="flex flex-col items-center group">
          <div class="bg-white/10 p-3 rounded-2xl group-hover:bg-white/20 transition-all duration-300 shadow-md">
            <i class="fas fa-chart-bar text-xl"></i>
          </div>
          <span class="text-xs mt-1 group-hover:text-blue-200 transition">Vote</span>
        </a>
      </li>

      <li>
        <a href="../candidates.php" class="flex flex-col items-center group">
          <div class="bg-white/10 p-3 rounded-2xl group-hover:bg-white/20 transition-all duration-300 shadow-md">
            <i class="fas fa-users text-xl"></i>
          </div>
          <span class="text-xs mt-1 group-hover:text-blue-200 transition">Candidates</span>
        </a>
      </li>


      <li>
        <a href="../student/profile.php" class="flex flex-col items-center hover:scale-110 transition-transform duration-300">
          <div class="bg-white/20 p-3 rounded-full shadow-lg ring-2 ring-blue-300/30">
            <i class="fas fa-user-circle text-3xl"></i>
          </div>
        </a>
      </li>
    </ul>

    <!-- Mobile Menu Button -->
    <button id="menu-btn" class="md:hidden text-white text-3xl focus:outline-none hover:text-blue-200">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <!-- Mobile Navigation (Bottom Bar Style like apps) -->
  <div id="mobile-menu" class="hidden fixed bottom-0 left-0 w-full bg-gradient-to-r from-[#0a2342] via-[#1e3a8a] to-[#3b82f6] flex justify-around py-3 text-white shadow-2xl rounded-t-3xl md:hidden">
    <a href="../student/welcome_home.php" class="flex flex-col items-center group">
      <i class="fas fa-house text-xl group-hover:text-blue-300 transition"></i>
      <span class="text-xs mt-1">Home</span>
    </a>
    
    <a href="../student/election_type.php" class="flex flex-col items-center group">
      <i class="fas fa-chart-bar text-xl group-hover:text-blue-300 transition"></i>
      <span class="text-xs mt-1">Vote</span>
    </a>

    <a href="../candidates.php" class="flex flex-col items-center group">
      <i class="fas fa-users text-xl group-hover:text-blue-300 transition"></i>
      <span class="text-xs mt-1">Candidates</span>
    </a>


  <button id="profile-btn" class="flex flex-col items-center hover:scale-110 transition-transform duration-300">
    <div class="bg-white/20 p-3 rounded-full shadow-lg ring-2 ring-blue-300/30">
      <i class="fas fa-user-circle text-3xl"></i>
    </div>
  </button>

  </div>
</nav>
<!-- Font Awesome & Tailwind CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>

<!-- Mobile Menu Toggle -->
<script>
  const menuBtn = document.getElementById('menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');

  menuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
    menuBtn.innerHTML = mobileMenu.classList.contains('hidden')
      ? '<i class="fas fa-bars"></i>'
      : '<i class="fas fa-times"></i>';
  });
</script>
<div class="pt-28"></div>
<div class="max-w-md mx-auto mt-10 bg-white rounded-lg shadow-md border border-gray-200 p-4">

    <!-- HEADER -->
    <div class="border-b pb-2 mb-4">
        <h1 class="text-xl font-bold text-gray-800">REVIEW MY VOTE</h1>
        <p class="text-xs text-gray-600 mt-1">
            Issued to:
            <span class="font-semibold text-gray-700"><?= htmlspecialchars($fullname) ?></span>
            (<?= htmlspecialchars($student_id) ?>)
        </p>
    </div>

<?php if ($voteResult->num_rows > 0): ?>
<?php
$currentElection = '';
while ($row = $voteResult->fetch_assoc()):
    if ($currentElection !== $row['election_type']):
        if ($currentElection !== '') echo '</tbody></table><br>';
        $currentElection = $row['election_type'];
?>
    <h2 class="text-lg font-semibold text-gray-800 mb-1 mt-4 border-b pb-1">
        <?= htmlspecialchars($currentElection) ?> Election
    </h2>

    <table class="w-full text-xs border-collapse shadow-sm">
        <thead class="bg-gray-900 text-white text-left">
            <tr>
                <th class="py-2 px-2 rounded-l-lg">Position</th>
                <th class="py-2 px-2 rounded-r-lg">Candidate</th>
            </tr>
        </thead>
        <tbody class="bg-gray-50 divide-y">
<?php endif; ?>

        <tr class="hover:bg-gray-100 transition">
            <td class="py-2 px-2 font-medium text-gray-700"><?= htmlspecialchars($row['position']) ?></td>
            <td class="py-2 px-2 text-gray-800"><?= htmlspecialchars($row['candidate_name']) ?></td>
        </tr>

<?php endwhile; ?>
        </tbody>
    </table>

<?php else: ?>
<p class="text-gray-500 text-center py-4 italic text-sm">No votes recorded.</p>
<?php endif; ?>

<div class="mt-5 text-center">
    <a href="../student/welcome_home.php"
       class="inline-block px-4 py-1.5 bg-gray-800 text-white text-xs rounded-md hover:bg-black transition">
        Back to Home
    </a>
</div>

</div>
</body>

</html>

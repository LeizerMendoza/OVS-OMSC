<?php
session_start();
include '../database.php';

$election_types = [];
$result = $conn->query("SELECT DISTINCT election_type FROM candidates");
while ($row = $result->fetch_assoc()) {
    $election_types[] = $row['election_type'];
}

$selected_type = isset($_GET['type'])
    ? $_GET['type']
    : (count($election_types) ? $election_types[0] : '');

$candidates = [];
if ($selected_type) {
    $stmt = $conn->prepare(
      "SELECT name, partylist, position, votes 
       FROM candidates 
       WHERE election_type = ? 
       ORDER BY partylist, votes DESC"
    );
    $stmt->bind_param("s", $selected_type);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $candidates[] = $r;
    }
    $stmt->close();
}
$party_groups = [];
foreach ($candidates as $c) {
    $party_groups[$c['partylist']][] = $c;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Election Results</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col min-h-screen bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 font-sans">
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
  <button id="profile-btn" class="flex flex-col items-center hover:scale-110 transition-transform duration-300">
    <div class="bg-white/20 p-3 rounded-full shadow-lg ring-2 ring-blue-300/30">
      <i class="fas fa-user-circle text-3xl"></i>
    </div>
  </button>
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

    <a href="../student/profile.php" class="flex flex-col items-center hover:scale-110 transition-transform duration-300">
      <i class="fas fa-user-circle text-3xl"></i>
    </a>
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

<main class="flex-grow pt-24 px-4 md:px-12 space-y-10 pb-10">
  <div class="max-w-7xl mx-auto space-y-6">

    <!-- Heading Section -->
    <div class="flex flex-col md:flex-row items-center justify-between bg-white shadow-lg rounded-xl p-6 mb-10">
      <div class="flex items-center space-x-4">
        <img src="../omsc-logo.png" alt="Logo" class="w-16 h-16">
        <h1 class="text-3xl font-extrabold text-indigo-700">Election Results</h1>
        <p class="text-xl font-bold text-green-500">Vote count in progress</p>
      </div>
      <form method="GET" class="mt-4 md:mt-0">
        <label for="type" class="font-semibold text-gray-700 mr-2">Select Election Type:</label>
        <select name="type" id="type" class="px-4 py-2 border rounded-lg bg-indigo-50 text-indigo-800 focus:ring-2 focus:ring-indigo-400 transition" onchange="this.form.submit()">
          <?php foreach ($election_types as $type): ?>
            <option value="<?= htmlspecialchars($type) ?>" <?= $type === $selected_type ? 'selected' : '' ?>>
              <?= htmlspecialchars($type) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>

    <!-- Cards Section: Grid Layout for Candidates -->
    <?php if (count($party_groups)): ?>
      <div class="grid gap-10 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-50">
        <?php foreach ($party_groups as $party => $group): ?>
          <div class="bg-white rounded-3xl shadow-xl overflow-hidden transform hover:scale-105 transition-all">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4">
              <h2 class="text-xl font-bold text-white"><?= htmlspecialchars($party) ?></h2>
            </div>
            <div class="p-4 space-y-4">
              <?php foreach ($group as $cand): ?>
                <div class="flex justify-between items-center border-b last-of-type:border-0 pb-2">
                  <div>
                    <p class="font-medium text-gray-800"><?= htmlspecialchars($cand['name']) ?></p>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($cand['position']) ?></p>
                  </div>
                  <div class="text-indigo-700 font-bold text-lg">
                    <?= (int)$cand['votes'] ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-center text-red-600 font-medium">No results available for this election type.</p>
    <?php endif; ?>
  </div>
</main>
<!-- 🌤️ Clean Blended Footer -->
<footer class="w-full text-gray-500 text-center py-6 mt-auto transition-all duration-300 ease-in-out bg-transparent">
  <div class="max-w-7xl mx-auto px-6 flex flex-col items-center space-y-2">
    <p class="text-sm font-medium tracking-wide">
      &copy; 2025 Occidental Mindoro State College — OMSC VoteSphere. All Rights Reserved.
    </p>
    <div class="flex justify-center space-x-6">
      <a href="https://www.facebook.com/setmamburao" target="_blank"
         class="hover:text-blue-500 transform hover:scale-110 transition duration-300" title="Facebook">
        <i class="fab fa-facebook text-lg"></i>
      </a>
      <a href="mailto:vote.support@omsc.edu.ph"
         class="hover:text-blue-500 transform hover:scale-110 transition duration-300" title="Email">
        <i class="fas fa-envelope text-lg"></i>
      </a>
    </div>
  </div>
</footer>

<script>
    document.getElementById("menu-toggle").addEventListener("click", () => {
      document.getElementById("mobile-menu").classList.toggle("hidden");
    });
  </script>

<style>
    .nav-link {
        position: relative;
        color: #E0AFA0;
        font-weight: 500;
        padding-bottom: 5px;
        transition: all 0.3s ease-in-out;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -3px;
        width: 0;
        height: 2px;
        background-color: rgb(255, 114, 71);
        transition: all 0.3s ease-in-out;
    }

    .nav-link:hover::after {
        left: 0;
        width: 100%;
    }

    .nav-link:hover {
        color: rgb(255, 114, 71);
        transform: translateY(-2px);
    }

    .active {
        color: rgb(255, 114, 71) !important; 
        font-weight: bold;
        text-decoration: underline;
    }

    .active:hover {
        color: rgb(255, 114, 71);
        text-decoration: underline;
    }
</style>
</body>
</html>

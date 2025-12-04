<?php
session_start();
include 'database.php';

$result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Messages | OMSC Elections</title>
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
        <a href="../candidates.php" class="flex flex-col items-center group">
          <div class="bg-white/10 p-3 rounded-2xl group-hover:bg-white/20 transition-all duration-300 shadow-md">
            <i class="fas fa-users text-xl"></i>
          </div>
          <span class="text-xs mt-1 group-hover:text-blue-200 transition">Candidates</span>
        </a>
      </li>

      <li>
        <a href="../student/view_results.php" class="flex flex-col items-center group">
          <div class="bg-white/10 p-3 rounded-2xl group-hover:bg-white/20 transition-all duration-300 shadow-md">
            <i class="fas fa-chart-bar text-xl"></i>
          </div>
          <span class="text-xs mt-1 group-hover:text-blue-200 transition">Results</span>
        </a>
      </li>

      <li>
        <a href="../profile.php" class="flex flex-col items-center hover:scale-110 transition-transform duration-300">
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

    <a href="../candidates.php" class="flex flex-col items-center group">
      <i class="fas fa-users text-xl group-hover:text-blue-300 transition"></i>
      <span class="text-xs mt-1">Candidates</span>
    </a>

    <a href="../student/view_results.php" class="flex flex-col items-center group">
      <i class="fas fa-chart-bar text-xl group-hover:text-blue-300 transition"></i>
      <span class="text-xs mt-1">Results</span>
    </a>

    <a href="../profile.php" class="flex flex-col items-center hover:scale-110 transition-transform duration-300">
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

  <a href="../admin/admin_dashboard.php" 
   class="no-print fixed top-20 left-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6 rounded-full shadow-2xl hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 font-semibold flex items-center space-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
        <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6z"/>
    </svg>
    <span>Back</span>
  </a>

  <!-- Page Content -->
  <div class="flex-grow mt-24 mb-10 flex justify-center">
  <div class="w-full max-w-3xl bg-white bg-opacity-90 rounded-2xl shadow-2xl p-8">
    
      <h1 class="text-2xl font-semibold text-[#0D1B2A] mb-4">📬 Students Messages</h1>

      <?php if ($result->num_rows > 0): ?>
        <div class="space-y-4">
          <?php while ($row = $result->fetch_assoc()): ?>
            <div class="flex items-start space-x-4 p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition">
              <img src="candidates/user.png" alt="User Avatar" class="w-8 h-8 rounded-full object-cover">
              <div class="flex flex-col">
                <p class="text-sm font-semibold text-indigo-900"><?= htmlspecialchars($row['name']) ?></p>
                <p class="text-xs text-gray-500 mb-2"><?= htmlspecialchars($row['email']) ?></p>
                <p class="text-sm text-gray-800"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                <p class="text-xs text-gray-400 mt-2">Received on: <?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?></p>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <p class="text-gray-500">No messages received yet.</p>
      <?php endif; ?>
    </div>
  </div>
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


  <!-- Toggle Script -->
  <script>
    const toggleBtn = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    toggleBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
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

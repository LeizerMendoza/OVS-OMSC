<?php
session_start();
include '../database.php';
$annRes = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>OMSC Election Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col h-screen bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 font-sans">
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

<main class="flex-grow px-4 md:px-12 mt-24 pb-10 space-y-8 bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100">

    <section class="bg-white p-8 rounded-3xl shadow-2xl border-l-[10px] border-[#E09F3E] hover:scale-[1.02] transition-transform duration-300">
        <h2 class="text-3xl font-extrabold text-[#0D1B2A] mb-6 flex items-center gap-3">
            🗳️ Current/Ongoing Elections
        </h2>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-yellow-100 via-orange-100 to-yellow-200 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-yellow-400">
                <h3 class="text-2xl font-bold text-[#0D1B2A] mb-2 flex items-center justify-between">
                    🏫 Student Leaders Elections 
                </h3>
                <p class="text-gray-700 mb-4 text-sm">📅 <strong>April 11</strong><br>Vote for your Student leaders!</p>
            </div>
        </div>
    </section>

    <section class="bg-white p-8 rounded-3xl shadow-2xl border-l-[10px] border-[#0077b6]">
        <h2 class="text-3xl font-extrabold text-[#0D1B2A] mb-6 flex items-center gap-3">
            📆 Election Timeline
        </h2>
        <ol class="relative border-l-4 border-[#0077b6] pl-6 space-y-6">
            <li class="ml-4">
                <span class="absolute -left-3 w-6 h-6 bg-[#0077b6] rounded-full border-4 border-white"></span>
                <p class="font-bold text-[#0D1B2A]">Campaign Period</p>
                <p class="text-gray-600 text-sm">April 7 – April 10</p>
            </li>
            <li class="ml-4">
                <span class="absolute -left-3 w-6 h-6 bg-[#E09F3E] rounded-full border-4 border-white"></span>
                <p class="font-bold text-[#0D1B2A]">Voting Day</p>
                <p class="text-gray-600 text-sm">April 11</p>
            </li>
            <li class="ml-4">
                <span class="absolute -left-3 w-6 h-6 bg-[#52b788] rounded-full border-4 border-white"></span>
                <p class="font-bold text-[#0D1B2A]">Results Release</p>
                <p class="text-gray-600 text-sm">April 12</p>
            </li>
        </ol>
    </section>

   <section class="bg-gradient-to-br from-white to-[#f0fdf4] p-14 rounded-3xl shadow-2xl border-l-[12px] border-[#52b788]">
   <div class="flex flex-col lg:flex-row items-center gap-8">
  <div class="w-full lg:w-1/2 flex justify-center">
    <img src="../candidates/vote.jpg" alt="Election Campaign"
         class="rounded-2xl shadow-lg w-full max-w-md h-auto object-cover border-4 border-[#d8f3dc]">
  </div>

    <div class="w-full lg:w-1/2">
      <h2 class="text-4xl font-bold text-[#0D1B2A] mb-4 flex items-center gap-3">
        👥 Meet the Candidates
      </h2>
      <p class="text-lg text-gray-700 leading-relaxed mb-6">
        Get to know the passionate and committed individuals running for office this year. Each candidate brings their own ideas, strengths, and vision for the future of our student body.
      </p>
      <a href="../candidates.php" class="inline-block bg-[#52b788] hover:bg-[#40916c] text-white font-semibold px-6 py-3 rounded-full transition shadow-lg">
        Meet the Candidates →
      </a>
    </div>
  </div>
</section>

<section class="bg-gradient-to-br from-white to-[#f0fdf4] p-12 rounded-3xl shadow-2xl border-l-[12px] border-[#e63946]">
  <div class="flex flex-col lg:flex-row items-center gap-8">
    <div class="w-full lg:w-1/2">
      <h2 class="text-4xl font-extrabold text-[#0D1B2A] mb-4 flex items-center gap-3">
        📢 Announcements
      </h2>
      <ul class="list-disc pl-5 text-gray-700 space-y-3">
        <li>Campaigning began on <strong>April 7</strong>. Check out candidate booths on campus.</li>
        <li>Voting starts <strong>April 11 at 8:00 AM</strong> and ends <strong>at 11:59 PM</strong>.</li>
        <li>Official results will be posted on <strong>April 12</strong>.</li>
        <li>Use only your official OMSC credentials to vote. Unauthorized access is prohibited.</li>
        <?php if (mysqli_num_rows($annRes) > 0): ?>
          <?php while ($ann = mysqli_fetch_assoc($annRes)): ?>
            <li>
              <p class="font-semibold"><?= htmlspecialchars($ann['title']) ?></p>
              <p class="text-sm"><?= nl2br(htmlspecialchars($ann['content'])) ?></p>
              <span class="text-xs text-gray-500"><?= date("M j, Y", strtotime($ann['created_at'])) ?></span>
            </li>
          <?php endwhile; ?>
        <?php endif; ?>
      </ul>
    </div>

    <div class="w-full lg:w-1/2 flex justify-center lg:justify-end">
      <img src="../candidates/attention.jpg" alt="Announcement Image"
           class="rounded-2xl shadow-lg w-full max-w-md h-auto object-cover border-4 border-[#f1f5f9] transition-transform transform hover:scale-105 duration-300">
    </div>
  </div>
</section>

    <section class="bg-white p-8 rounded-3xl shadow-2xl border-l-[10px] border-[#6a4c93]">
        <h2 class="text-3xl font-extrabold text-[#0D1B2A] mb-6">📜 How to Vote</h2>
        <ol class="list-decimal pl-5 space-y-3 text-gray-800">
            <li>Log in using your OMSC student credentials.</li>
            <li>Select the current election.</li>
            <li>Choose candidates for each position.</li>
            <li>Review your selections carefully.</li>
            <li>Submit your vote. A confirmation message will appear.</li>
        </ol>
        <p class="text-sm text-gray-500 mt-3 italic">⚠️ Only one vote per student is allowed. Double-check before submitting!</p>
    </section>
    <a href="../admin/admin_dashboard.php" 
   class="no-print fixed top-10 left-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6 rounded-full shadow-2xl hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 font-semibold flex items-center space-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
        <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6z"/>
    </svg>
    <span>Back</span>
  </a>

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
            const menu = document.getElementById("mobile-menu");
            menu.classList.toggle("hidden");
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

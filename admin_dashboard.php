<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col h-screen bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 font-sans">
<!-- 🌐 Modern App-Style Navigation Bar (Adopted Design) -->
<nav class="bg-gradient-to-r from-[#0a2342] via-[#1e3a8a] to-[#3b82f6] shadow-xl fixed top-0 left-0 w-full z-50 transition-all duration-500 ease-in-out backdrop-blur-md">
  <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-20 text-white relative">

    <!-- Logo and Title -->
    <a href="../student/welcome_home.php" class="flex items-center space-x-4 hover:scale-105 transition-transform duration-300">
      <img src="../omsc-logo.png" alt="OMSC Logo" class="w-16 h-16 rounded-full shadow-md border border-white/40">
      <div class="flex flex-col leading-tight">
        <h1 class="text-lg md:text-xl font-semibold tracking-wide">Occidental Mindoro State College</h1>
        <p class="text-sm md:text-base text-blue-100 font-medium">OMSC VoteSphere — Student Election</p>
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
    </ul>

    <!-- Mobile Menu Button -->
    <button id="menu-btn" class="md:hidden text-white text-3xl focus:outline-none hover:text-blue-200">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <!-- Mobile Navigation (Bottom Navigation Bar Style) -->
  <div id="mobile-menu" class="hidden fixed bottom-0 left-0 w-full bg-gradient-to-r from-[#0a2342] via-[#1e3a8a] to-[#3b82f6] flex justify-around py-3 text-white shadow-2xl rounded-t-3xl md:hidden">
    <a href="../student/welcome_home.php" class="flex flex-col items-center group">
      <i class="fas fa-house text-xl group-hover:text-blue-300 transition"></i>
      <span class="text-xs mt-1">Home</span>
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

<!-- ✅ TailwindCSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- ✅ Dashboard Section -->
<div class="min-h-screen flex flex-col items-center justify-center px-6 bg-gradient-to-br from-blue-50 via-white to-blue-100">
  <div class="bg-white/90 backdrop-blur-sm p-10 rounded-2xl shadow-2xl text-center w-full max-w-2xl border border-blue-100 transition-all duration-500 hover:shadow-blue-200/70">
    <h1 class="text-4xl font-extrabold text-blue-900 mb-2 tracking-wide">Admin Dashboard</h1>
    <p class="text-gray-600 mb-8">Manage student elections and oversee the voting process efficiently.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <a href="../admin/add_candidates.php" 
         class="group bg-gradient-to-r from-blue-400 to-blue-600 text-white py-3 rounded-lg font-medium shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 ease-in-out flex items-center justify-center space-x-2">
         <i class="fas fa-user-plus text-white text-lg group-hover:rotate-12 transition-transform duration-300"></i>
         <span>Add Candidates</span>
      </a>

      <a href="../admin/admin_announcements.php" 
         class="group bg-gradient-to-r from-sky-500 to-blue-700 text-white py-3 rounded-lg font-medium shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 ease-in-out flex items-center justify-center space-x-2">
         <i class="fas fa-bullhorn text-white text-lg group-hover:rotate-12 transition-transform duration-300"></i>
         <span>Create Announcement</span>
      </a>

      <a href="../admin/students_list.php" 
         class="group bg-gradient-to-r from-indigo-500 to-blue-700 text-white py-3 rounded-lg font-medium shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 ease-in-out flex items-center justify-center space-x-2">
         <i class="fas fa-database text-white text-lg group-hover:rotate-12 transition-transform duration-300"></i>
         <span>Student Voting Data Records</span>
      </a>

      <a href="../admin/election_results.php" 
         class="group bg-gradient-to-r from-gray-700 to-gray-900 text-white py-3 rounded-lg font-medium shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 ease-in-out flex items-center justify-center space-x-2">
         <i class="fas fa-chart-bar text-white text-lg group-hover:rotate-12 transition-transform duration-300"></i>
         <span>View Election Results</span>
      </a>
    </div>
  </div>

  <a href="../logout.php" 
     class="mt-8 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium py-2.5 px-8 rounded-lg shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 flex items-center space-x-2">
     <i class="fas fa-sign-out-alt"></i>
     <span>Logout</span>
  </a>
</div>

<!-- ✅ Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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

    <script src="app.js"></script>

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
            background-color:rgb(255, 114, 71);
            transition: all 0.3s ease-in-out;
        }
        .nav-link:hover::after {
            left: 0;
            width: 100%;
        }
        .nav-link:hover {
            color:rgb(255, 114, 71);
            transform: translateY(-2px);
        }
    </style>
</body>
</html>

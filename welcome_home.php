

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

  <li>
  <button id="profile-btn-mobile" class="flex flex-col items-center hover:scale-110 transition-transform duration-300">
    <div class="bg-white/20 p-3 rounded-full shadow-lg ring-2 ring-blue-300/30">
      <i class="fas fa-user-circle text-3xl"></i>
    </div>
</button>

</li>
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
<main class="flex-grow bg-[#f5f7fa] text-gray-800 mt-24">

    <!-- ONGOING ELECTION -->
    <section class="py-16 px-14 bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold text-[#0a2e5c] tracking-tight mb-6">
                Ongoing Election
            </h2>

            <div class="bg-[#0a2e5c] text-white p-10 rounded-md shadow-lg relative overflow-hidden">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-semibold">Student Leaders Election</h3>
                    <span class="bg-green-500 text-white text-xs px-3 py-1 rounded uppercase tracking-wide">Live</span>
                </div>

                <p class="mt-3 text-sm opacity-85">
                    Election Date: <strong>April 11</strong> — Participate and make an informed choice.
                </p>

                <a href="../student/election_type.php"
                   class="inline-block mt-6 text-sm font-semibold bg-white text-[#0a2e5c] hover:bg-gray-100 px-6 py-2 rounded shadow-sm transition">
                    Proceed to Vote
                </a>
                <!-- View Partial Election Results Button -->
<a href="student_partial_results.php" 
   class="block w-full text-center bg-gradient-to-r from-blue-600 to-blue-800 
          text-white font-semibold py-3 rounded-xl shadow-md hover:from-blue-700 
          hover:to-blue-900 transition mt-4">
    📊 View Partial Election Results
</a>

            </div>
        </div>
    </section>

    <!-- TIMELINE -->
    <section class="py-20 px-14 bg-[#f9fbff] border-b border-gray-200">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold text-[#0a2e5c] tracking-tight mb-10">
                Election Timeline
            </h2>

            <div class="relative border-l-2 border-[#1b73e8] pl-8 space-y-12">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Campaign Period</h3>
                    <p class="text-sm text-gray-600">April 7 – April 10</p>
                </div>

                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Voting Day</h3>
                    <p class="text-sm text-gray-600">April 11</p>
                </div>

                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Results Announcement</h3>
                    <p class="text-sm text-gray-600">April 12</p>
                </div>
            </div>
        </div>
    </section>

    <!-- MEET THE CANDIDATES -->
    <section class="py-24 px-14 bg-white border-b border-gray-200">
        <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-20 items-center">
            <img src="../candidates/vote.jpg"
                 class="w-full rounded-lg shadow-md object-cover">

            <div>
                <h2 class="text-3xl font-bold text-[#0a2e5c] mb-4">
                    Meet the Candidates
                </h2>
                <p class="leading-relaxed text-gray-700 mb-6">
                    Explore the profiles, platforms, and advocacies of each candidate. Make well-informed decisions based on their vision and leadership direction for OMSC.
                </p>

                <a href="../candidates.php"
                   class="inline-block bg-[#1b73e8] hover:bg-[#145bb6] text-white px-8 py-3 shadow-sm text-sm font-semibold rounded transition">
                    View Candidate Profiles
                </a>
            </div>
        </div>
    </section>

    <!-- ANNOUNCEMENTS -->
    <section class="py-24 px-14 bg-[#f9fbff] border-b border-gray-200">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-[#0a2e5c] mb-10 tracking-tight">
                Announcements
            </h2>

            <?php if(mysqli_num_rows($annRes) > 0): ?>
            <div class="space-y-14">
                <?php while($ann = mysqli_fetch_assoc($annRes)): ?>
                <article class="pb-10 border-b border-gray-300">
                    <?php if($ann['image']): ?>
                        <img src="../<?= htmlspecialchars($ann['image']) ?>"
                             class="w-full max-w-md mb-4 rounded shadow-sm">
                    <?php endif; ?>

                    <h3 class="text-2xl font-semibold text-gray-900"><?= htmlspecialchars($ann['title']) ?></h3>
                    <span class="text-xs text-[#1b73e8] uppercase tracking-wide">
                        <?= htmlspecialchars($ann['category']) ?>
                    </span>

                    <p class="mt-4 leading-relaxed text-gray-800">
                        <?= nl2br(htmlspecialchars($ann['content'])) ?>
                    </p>

                    <p class="text-xs text-gray-500 mt-3">
                        Posted on <?= date("M j, Y", strtotime($ann['created_at'])) ?>
                    </p>
                </article>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
                <p class="text-gray-500">No announcements are available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- HOW TO VOTE -->
    <section class="py-20 px-14 bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold text-[#0a2e5c] mb-8 tracking-tight">
                How to Vote
            </h2>

            <ol class="list-decimal pl-6 space-y-4 text-gray-700 text-lg">
                <li>Log in using your official OMSC credentials.</li>
                <li>Select the current active election.</li>
                <li>Choose your desired candidates.</li>
                <li>Review your selections carefully.</li>
                <li>Submit your vote and wait for confirmation.</li>
            </ol>
        </div>
    </section>

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
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>


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
<?php include '../student/profile_data.php'; ?>
<?php include '../student/profile_overlay.php'; ?>
</body>
</html>

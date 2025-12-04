<?php
session_start();
include '../database.php';

$positions = [
    'SSG' => ['Governor', 'Vice Governor', 'Board Member 1', 'Board Member 2', 'Board Member 3', 'Board Member 4', 'Board Member 5','Board Member 6', 'Board Member 7','Board Member 8', 'Board Member 9'],
    'PADC' => ['Mayor', 'Vice Mayor', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'YMO' => ['Mayor', 'Vice Mayor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7', 'Councilor 8'],
    'YES' => ['Mayor', 'Vice Mayor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7', 'Councilor 8'],
    'LSC' => ['President', 'Vice President', 'Secretary', 'Treasurer','Auditor', 'PIO','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'SPORTS CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer', 'Asst. Treasurer','Auditor', 'PIO', 'Project Manager', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7', 'Councilor 8', 'Councilor 9', 'Councilor 10'],
    'ENGLISH CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer','Auditor', 'PIO', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'SCI-MATH CLUB' => ['President', 'Vice President', 'Secretary', 'Treasurer','Auditor', 'PIO','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'CYMA CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer','Auditor', 'PIO 1', 'PIO 2',  'Project Manager','Muse','Escort'],
    'UMSO CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary','Treasurer','Auditor', 'PIO', 'Project Manager'],
    'SAMFILKO' => ['Pangulo', 'Ikalawang Pangulo', 'Kalihim', 'Ingat Yaman','Taga-Suri', 'Tagapamayapa', 'Tagapangasiwang Proyekto'],
    'SECTION' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer', 'Asst. Treasurer','Auditor','PIO 1', 'PIO 2', 'Project Manager','Muse', 'Escort'],
];

$partylist_elections = ['SSG', 'PADC', 'YMO', 'YES'];

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['election_type'])) {
    $student_id = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $position = trim($_POST['position']);
    $election_type = $_POST['election_type'];
    $partylist = in_array($election_type, $partylist_elections) ? trim($_POST['partylist']) : null;    
    if (!empty($_POST['election_type'])) {
        $_SESSION['selected_election_type'] = $_POST['election_type'];
    }

    

    if (!empty($name) && !empty($position) && !empty($election_type)) {
        if (in_array($election_type, $partylist_elections)) {
            $query = "INSERT INTO candidates (name, position, election_type, partylist) VALUES (?, ?, ?, ?)";
        } else {
            $query = "INSERT INTO candidates (name, position, election_type) VALUES (?, ?, ?)";
        }

        if ($stmt = $conn->prepare($query)) {
            if (in_array($election_type, $partylist_elections)) {
                $stmt->bind_param("ssss", $name, $position, $election_type, $partylist);
            } else {
                $stmt->bind_param("sss", $name, $position, $election_type);
            }

            if ($stmt->execute()) {
                $message = "<p class='text-green-600 text-center'>✅ Candidate added successfully!</p>";
            } else {
                $message = "<p class='text-red-600 text-center'>❌ Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            $message = "<p class='text-red-600 text-center'>❌ Error preparing statement: " . $conn->error . "</p>";
        }
    } else {
        $message = "<p class='text-red-600 text-center'>⚠️ All fields are required!</p>";
    }
}

$selected = isset($_SESSION['selected_election_type']) ? $_SESSION['selected_election_type'] : '';

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Candidate</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col min-h-screen bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 font-sans">
 <!-- 🧭 Professional White and Blue Gradient Navigation Bar -->
<nav class="bg-gradient-to-r from-[#0a2342] via-[#1e3a8a] to-[#3b82f6] shadow-lg fixed top-0 left-0 w-full z-50 transition-all duration-500 ease-in-out">
  <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-20 text-white relative">

    <a href="../student/welcome_home.php" class="flex items-center space-x-4 hover:scale-105 transition-transform duration-300">
      <img src="../omsc-logo.png" alt="OMSC Logo" class="w-16 h-16 rounded-full shadow-md border border-white/40">
      <div class="flex flex-col leading-tight">
        <h1 class="text-lg md:text-xl font-semibold tracking-wide">Occidental Mindoro State College</h1>
        <p class="text-sm md:text-base text-blue-100 font-medium">OMSC VoteSphere — Student Election</p>
      </div>
    </a>

    <!-- Desktop Navigation -->
    <ul class="hidden md:flex space-x-8 font-medium text-white">
      <li><a href="welcome_home.php" class="hover:text-blue-200 transition-colors">Home</a></li>
    </ul>

    <!-- Mobile Menu Button -->
    <button id="menu-btn" class="md:hidden text-white text-3xl focus:outline-none hover:text-blue-200">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <!-- Mobile Navigation Links -->
  <div id="mobile-menu" class="hidden flex-col items-center bg-[#0a2342] text-white py-4 space-y-4 shadow-lg md:hidden transition-all duration-500">
    <a href="../student/welcome_home.php" class="hover:text-blue-200 transition-colors">Home</a>
  </div>
</nav>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>

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

<!-- ✅ Back Button -->
<a href="../admin/admin_dashboard.php" 
   class="no-print fixed top-20 left-6 bg-gradient-to-r from-blue-500 to-blue-700 text-white py-3 px-6 rounded-full shadow-lg hover:shadow-2xl hover:from-blue-600 hover:to-blue-800 transform hover:scale-110 transition-all duration-300 font-semibold flex items-center space-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
        <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6z"/>
    </svg>
    <span>Back</span>
</a>

<!-- ✅ Main Content Wrapper -->
<div class="flex-grow mt-24 flex items-center justify-center px-4 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">

  <!-- ✅ Main Card -->
  <div class="bg-white/95 backdrop-blur-sm p-10 rounded-2xl shadow-2xl w-full max-w-5xl border border-blue-100 hover:shadow-blue-200/70 transition-all duration-500">
    
    <!-- ✅ Header -->
    <div class="flex justify-between items-center mb-6">
      <div class="flex items-center gap-4">
        <img src="../omsc-logo.png" alt="OMSC Logo" class="w-16 h-16 hover:scale-110 transition-all duration-300">
        <div>
          <h1 class="text-3xl font-extrabold text-blue-900 tracking-wide">Add a New Candidate</h1>
          <p class="text-gray-600 text-sm">Input candidate details and assign to election type.</p>
        </div>
      </div>
      <a href="../admin/election_results.php" 
         class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-5 py-2 rounded-lg shadow-md hover:from-blue-700 hover:to-indigo-800 hover:shadow-lg transform hover:scale-105 transition-all duration-300">
        📊 View Results
      </a>
    </div>

    <!-- ✅ PHP Message -->
    <?= $message ?>

    <!-- ✅ Election Type Selector -->
    <div class="mb-6">
      <label for="electionType" class="block text-lg font-semibold text-gray-800 mb-1">Select Election Type:</label>
      <select id="electionType" 
              class="w-full px-4 py-2 border border-blue-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all duration-300">
        <option value="" disabled <?= $selected === '' ? 'selected' : '' ?>>Choose an Election Type</option>
        <?php foreach ($positions as $type => $posList) { ?>
          <option value="<?= $type ?>" <?= $selected === $type ? 'selected' : '' ?>><?= $type ?></option>
        <?php } ?>
      </select>
    </div>

    <!-- ✅ Candidate Forms -->
    <?php foreach ($positions as $type => $posList) { ?>
      <form method="POST" class="election-form hidden" id="form-<?= $type ?>">
        <input type="hidden" name="election_type" value="<?= $type ?>">

        <div class="grid md:grid-cols-2 gap-6 mb-6">
          <div>
            <label class="block font-medium text-gray-800 mb-1">Candidate Name</label>
            <input type="text" name="name" placeholder="Juan Dela Cruz" required 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all duration-300">
          </div>

          <div>
            <label class="block font-medium text-gray-800 mb-1">Student ID</label>
            <input type="text" name="student_id" placeholder="e.g. 12-3-45678" required 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all duration-300">
          </div>

          <?php if (in_array($type, $partylist_elections)) { ?>
            <div>
              <label class="block font-medium text-gray-800 mb-1">Partylist</label>
              <input type="text" name="partylist" placeholder="e.g. Lakas Partylist" required 
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all duration-300">
            </div>
          <?php } ?>

          <div class="md:col-span-2">
            <label class="block font-medium text-gray-800 mb-1">Position</label>
            <select name="position" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all duration-300">
              <?php foreach ($posList as $position) { ?>
                <option value="<?= $position ?>"><?= $position ?></option>
              <?php } ?>
            </select>
          </div>
        </div>

        <!-- ✅ Submit Button -->
        <button type="submit" 
                class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold py-3 rounded-lg shadow-md hover:from-green-600 hover:to-green-700 hover:shadow-lg transform hover:scale-105 transition-all duration-300">
          ➕ Add Candidate
        </button>
      </form>
    <?php } ?>

  </div>
</div>

<!-- ✅ Optional Animation for Fade-in -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.election-form').forEach(form => {
      form.style.opacity = '0';
      form.style.transition = 'opacity 0.5s ease';
    });

    const electionType = document.getElementById("electionType");
    electionType.addEventListener("change", () => {
      document.querySelectorAll('.election-form').forEach(form => form.classList.add('hidden'));
      const selected = document.getElementById("form-" + electionType.value);
      if (selected) {
        selected.classList.remove('hidden');
        setTimeout(() => selected.style.opacity = '1', 50);
      }
    });
  });
</script>

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
</style>


    <script>
        const forms = document.querySelectorAll('.election-form');
        const electionSelect = document.getElementById('electionType');
        const selectedValue = electionSelect.value;
        forms.forEach(f => f.classList.add('hidden'));
        if (selectedValue) {
            const visibleForm = document.getElementById('form-' + selectedValue);
            if (visibleForm) visibleForm.classList.remove('hidden');
        }
        electionSelect.addEventListener('change', function () {
            forms.forEach(f => f.classList.add('hidden'));
            const newForm = document.getElementById('form-' + this.value);
            if (newForm) newForm.classList.remove('hidden');
        });
    </script>

</body>
</html>

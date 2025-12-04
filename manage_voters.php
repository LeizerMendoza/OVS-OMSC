<?php
session_start();
include '../database.php';

// Fetch all voters
$query = "
SELECT 
    u.id,
    u.student_id,
    u.fullname,
    u.email,
    u.contact_no,
    u.section,
    u.course,
    u.club,
    u.has_voted
FROM users u
ORDER BY u.course, u.club, u.fullname
";
$res = mysqli_query($conn, $query);
if (!$res) die("Database query failed: " . mysqli_error($conn));


// Map courses to election types
$courseMap = [
    'BSIT' => 'PADC',
    'BEED' => 'YMO',
    'CBAM' => 'YES'
];

// Fetch all voters into array
$voters = [];
while ($v = mysqli_fetch_assoc($res)) {
    $v['mapped_course'] = $courseMap[$v['course']] ?? '';
    $voters[] = $v;
}

// Dropdown options
$types = ['SSG', 'LSC'];
$courses = ['PADC', 'YMO', 'YES'];
$clubs   = ['SPORTS CLUB', 'ENGLISH CLUB', 'SCI-MATH CLUB', 'CYMA', 'UMSO', 'SAMFILKO'];

// Merge dropdown: types + courses + clubs
$allOptions = array_merge($types, $courses, $clubs);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Voters | OMSCVoteSphere</title>
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

<main class="flex-1 md:ml-64 p-4 md:p-6">
  <!-- Filter/Search -->
  <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 mb-6">
  <!-- SECTION TITLE -->
  <div class="flex items-center gap-2 mb-4">
    <i class="fas fa-filter text-[#0a2342]"></i>
    <h2 class="text-lg font-semibold text-[#0a2342] tracking-tight">
      Filter & Search Voters
    </h2>
  </div>

  <!-- FILTER CONTROLS -->
  <div class="flex flex-col md:flex-row flex-wrap gap-3 md:gap-4">

    <!-- DROPDOWN -->
    <select id="voterFilter"
      class="p-2 border border-gray-300 rounded-md w-full md:w-64 focus:border-[#0a2342] focus:ring-1 focus:ring-[#0a2342] transition">
      <option value="ALL" selected>Election Types</option>
      <?php foreach($allOptions as $opt): ?>
        <option value="<?= strtoupper($opt) ?>"><?= strtoupper($opt) ?></option>
      <?php endforeach; ?>
    </select>

    <!-- SEARCH INPUT -->
    <input type="text"
      id="voterSearch"
      placeholder="Search by ID, name, course, club..."
      class="p-2 border border-gray-300 rounded-md w-full md:w-64 focus:border-[#0a2342] focus:ring-1 focus:ring-[#0a2342] transition" />

    <!-- SEARCH BUTTON -->
    <button id="searchBtn"
      class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-md bg-[#0a2342] text-white text-sm shadow-sm hover:bg-[#0c2d57] transition w-full md:w-auto">
      <i class="fas fa-search"></i>
      Search
    </button>
  </div>
</div>


  <!-- Table -->
  <div class="bg-white shadow rounded overflow-x-auto border border-blue-200">
    <table class="w-full text-left min-w-[900px] md:min-w-[1000px]" id="votersTable">
      <thead class="bg-blue-700 text-white text-sm md:text-base">
        <tr>
          <th class="px-3 py-2">#</th>
          <th class="px-3 py-2">Student ID</th>
          <th class="px-3 py-2">Fullname</th>
          <th class="px-3 py-2 hidden md:table-cell">Email</th>
          <th class="px-3 py-2 hidden md:table-cell">Contact</th>
          <th class="px-3 py-2 hidden md:table-cell">Section</th>
          <th class="px-3 py-2">Organization</th>
          <th class="px-3 py-2">Club(s)</th>
          <th class="px-3 py-2">Voted</th>
          <th class="px-3 py-2">Actions</th>
        </tr>
      </thead>
      <tbody class="text-sm md:text-base">
        <?php $count=1; foreach($voters as $v): ?>
        <tr class="border-b hover:bg-blue-50 voterRow">
          <td class="px-3 py-2"><?= $count++ ?></td>
          <td class="px-3 py-2"><?= htmlspecialchars($v['student_id']) ?></td>
          <td class="px-3 py-2"><?= htmlspecialchars($v['fullname']) ?></td>
          <td class="px-3 py-2 hidden md:table-cell"><?= htmlspecialchars($v['email']) ?></td>
          <td class="px-3 py-2 hidden md:table-cell"><?= htmlspecialchars($v['contact_no']) ?></td>
          <td class="px-3 py-2 hidden md:table-cell"><?= htmlspecialchars($v['section']) ?></td>
          <td class="px-3 py-4"><?= htmlspecialchars($v['mapped_course'] ?: $v['course']) ?></td>
          <td class="px-3 py-2"><?= htmlspecialchars($v['club']) ?: 'None' ?></td>
      <td class="px-3 py-2 text-sm <?= $v['has_voted'] ? 'text-green-600' : 'text-red-600' ?>">
    <?= $v['has_voted'] ? 'Yes' : 'No' ?>
</td>

<td class="px-2 py-2">
  <div class="flex flex-col items-start gap-1 w-24"> <!-- narrow fixed width -->

 <?php if ($v['has_voted']): ?>
<a href="view_vote.php?user_id=<?= $v['id'] ?>"
   class="flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200 transition w-full">
   <i class="fas fa-eye"></i> View Vote
   </a>

<?php else: ?>
<span class="flex items-center gap-1 px-2 py-1 text-xs font-medium text-gray-400 bg-gray-100 rounded-md w-full cursor-not-allowed">
   <i class="fas fa-eye-slash"></i> No Vote
</span>
<?php endif; ?>

    <!-- EDIT -->
    <a href="?action=edit&id=<?= $v['id'] ?>"
       class="flex items-center gap-1 px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-md hover:bg-yellow-200 transition w-full">
       <i class="fas fa-edit"></i> Edit
    </a>

    <!-- DELETE -->
    <a href="?action=delete&id=<?= $v['id'] ?>"
       onclick="return confirm('Delete voter <?= htmlspecialchars($v['fullname']) ?>?')"
       class="flex items-center gap-1 px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 transition w-full">
       <i class="fas fa-trash"></i> Delete
    </a>
  </div>
</td>

        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<script>
// Dropdown filter with multi-club support
document.getElementById('voterFilter').addEventListener('change', function(){
    const filter = this.value.toUpperCase();
    const clubOptions = ['SPORTS CLUB', 'ENGLISH CLUB', 'SCI-MATH CLUB', 'CYMA', 'UMSO', 'SAMFILKO'];

    document.querySelectorAll('.voterRow').forEach(row=>{
        const course = row.cells[6].innerText.toUpperCase();
        const clubStr = row.cells[7].innerText.toUpperCase();
        const clubs = clubStr.split(',').map(c => c.trim());

        if(filter === 'ALL') { row.style.display = ''; return; }
        if(['SSG','LSC'].includes(filter)) { row.style.display = ''; return; }
        if(['PADC','YMO','YES'].includes(filter)) { row.style.display = (course === filter) ? '' : 'none'; return; }
        if(clubOptions.includes(filter)) { row.style.display = clubs.includes(filter) ? '' : 'none'; return; }
        row.style.display = 'none';
    });
});

// Search functionality
document.getElementById('searchBtn').addEventListener('click', function(){
    const q = document.getElementById('voterSearch').value.toLowerCase();
    document.querySelectorAll('.voterRow').forEach(row=>{
        row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>

</body>
</html>

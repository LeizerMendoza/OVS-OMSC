<?php
session_start();
include '../database.php';

if (!$conn) {
    die("Database connection failed.");
}

$positions = [
    'SSG' => ['Governor', 'Vice Governor', 'Board Member 1', 'Board Member 2', 'Board Member 3', 'Board Member 4', 'Board Member 5','Board Member 6', 'Board Member 7','Board Member 8', 'Board Member 9'],
    'PADC' => ['Mayor', 'Vice Mayor','Secretary', 'Treasurer','Auditor', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'YMO' => ['Mayor', 'Vice Mayor', 'Secretary', 'Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'YES' => ['Mayor', 'Vice Mayor', 'Secretary', 'Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'LSC' => ['Mayor', 'Vice Mayor', 'Secretary', 'Asst. Secretary','Treasurer','Asst. Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'SPORTS CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer', 'Asst. Treasurer','Auditor', 'PIO', 'Project Manager', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7', 'Councilor 8', 'Councilor 9', 'Councilor 10'],
    'ENGLISH CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer','Auditor', 'PIO', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'SCI-MATH CLUB' => ['President', 'Vice President', 'Secretary', 'Treasurer','Auditor', 'PIO','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'CYMA' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer','Auditor', 'PIO 1', 'PIO 2',  'Project Manager','Muse','Escort'],
    'UMSO' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary','Treasurer','Auditor', 'PIO', 'Project Manager'],
    'UMSO CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary','Treasurer','Auditor', 'PIO', 'Project Manager'],
    'SAMFILKO' => ['Pangulo', 'Ikalawang Pangulo', 'Kalihim', 'Ingat Yaman','Taga-Suri', 'Tagapamayapa', 'Tagapangasiwang Proyekto'],
    'SECTION' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer', 'Asst. Treasurer','Auditor','PIO 1', 'PIO 2', 'Project Manager','Muse', 'Escort'],
];

/* ================= FETCH ELECTION TYPES ================= */
$election_types = [];
$typeQuery = "SELECT DISTINCT election_type FROM candidates";
$typeResult = $conn->query($typeQuery);

if ($typeResult && $typeResult->num_rows > 0) {
    while ($row = $typeResult->fetch_assoc()) {
        $election_types[] = trim($row['election_type']);
    }
}

/* ================= FETCH RESULTS ================= */
$all_election_results = [];
$sql = "SELECT election_type, position, name, student_id, COALESCE(partylist,'Independent') AS party, votes 
        FROM candidates 
        ORDER BY election_type, position, votes DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $etype = $row['election_type'];
        $position = $row['position'];
   $all_election_results[$etype][$position][] = [
    'name' => $row['name'],
    'student_id' => $row['student_id'], // <-- add this
    'position' => $position,
    'party' => $row['party'],
    'votes' => (int)$row['votes']
];

    }
}

/* ================= SET ORDERED TYPES ================= */
$orderedTypes = [];
foreach ($positions as $type => $posList) {
    if (isset($all_election_results[$type])) {
        $orderedTypes[] = $type;
    }
}
$selectedType = $_GET['etype'] ?? 'all';

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Election Results | Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-blue-50 text-gray-800">

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
    <li><a href="../admin/announcements.html" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-bullhorn text-blue-600 w-5"></i><span class="ml-3">Announcements</span></a></li>
    <li><a href="../admin/settings.html" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-cogs text-blue-600 w-5"></i><span class="ml-3">Settings</span></a></li>
    <li><a href="../logout.php" class="flex items-center px-6 py-3 hover:bg-red-50 border-l-4 border-transparent hover:border-red-500 transition"><i class="fas fa-sign-out-alt text-red-500 w-5"></i><span class="ml-3">Logout</span></a></li>
  </ul>
</aside>

<!-- Active Page Highlighter Script -->
<script>
  // Get current page filename
  const currentPage = window.location.pathname.split("/").pop();

  // Loop through all sidebar links
  document.querySelectorAll(".sidebar-link").forEach(link => {
    if (link.getAttribute("href").includes(currentPage)) {
      link.classList.add("bg-blue-100", "border-blue-500");
      link.classList.remove("border-transparent");
    }
  });
</script>

<main class="ml-64 w-full px-6 py-6 space-y-6">

<!-- HEADER & ELECTION SELECTOR -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-semibold text-blue-800">Election Results</h1>
    <p class="text-xs text-gray-600">Live vote tally • Auto-refresh: 1min</p>
  </div>

  <div class="flex items-center gap-2">
    <form method="GET">
      <label class="text-sm text-blue-800 font-medium">View Election:</label>
      <select name="etype" onchange="this.form.submit()" class="bg-white border border-blue-300 rounded-md px-3 py-1.5 text-sm text-gray-700">
        <option value="all" <?= ($selectedType=='all')?'selected':'' ?>>All Elections</option>
        <?php foreach($orderedTypes as $et): ?>
          <option value="<?= $et ?>" <?= ($selectedType==$et)?'selected':'' ?>><?= htmlspecialchars($et) ?></option>
        <?php endforeach; ?>
      </select>
    </form>
    <button onclick="location.reload()" class="ml-4 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Refresh</button>
    <button onclick="exportCSV()" class="ml-2 bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Export CSV</button>
  </div>
</div>

<?php foreach ($orderedTypes as $etype): ?>
<?php if($selectedType != 'all' && $selectedType != $etype) continue; ?>
<?php
    $positionData = $all_election_results[$etype];

 // Correct ordered positions based on predefined list
$mergedPositions = [];
$orderList = $positions[$etype]; // <-- strict correct order

$boardMembers = [];
$councilors = [];

// Step 1: Collect board/council positions separately
foreach ($positionData as $position => $candList) {
    if ($etype === 'SSG' && stripos($position, 'Board Member') !== false) {
        $boardMembers = array_merge($boardMembers, $candList);
    } elseif ($etype !== 'SSG' && stripos($position, 'Councilor') !== false) {
        $councilors = array_merge($councilors, $candList);
    }
}

// Step 2: Build positions following correct order
foreach ($orderList as $pos) {

    // SSG → Merge Board Member 1–9 → show as "Board Members"
    if ($etype === 'SSG' && stripos($pos, 'Board Member') !== false) {
        if (!isset($mergedPositions['Board Members']) && !empty($boardMembers)) {
            $mergedPositions['Board Members'] = $boardMembers;
        }
        continue;
    }

    // Clubs → Merge Councilor 1–10 → show as "Councilors"
    if ($etype !== 'SSG' && stripos($pos, 'Councilor') !== false) {
        if (!isset($mergedPositions['Councilors']) && !empty($councilors)) {
            $mergedPositions['Councilors'] = $councilors;
        }
        continue;
    }

    // Normal single positions
    if (isset($positionData[$pos])) {
        $mergedPositions[$pos] = $positionData[$pos];
    }
}


    // Calculate total votes for this election
    $totalVotes = 0;
    foreach ($mergedPositions as $pos => $cands) {
        foreach ($cands as $c) { $totalVotes += $c['votes']; }
    }
?>

<div class="bg-white rounded-2xl shadow-lg border border-blue-100 p-6">
  <div class="flex justify-between items-center bg-blue-50 px-4 py-3 rounded-lg border border-blue-200 mb-4">
    <h2 class="text-lg font-bold text-blue-700"><?= htmlspecialchars($etype) ?> Election</h2>
    <span class="text-xs text-gray-600">Total Votes: <span class="font-semibold"><?= $totalVotes ?></span> • Status: <span class="text-green-600 font-semibold">LIVE</span></span>
  </div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

    <?php foreach ($mergedPositions as $position => $candidates): ?>
      <?php usort($candidates, fn($a,$b)=>$b['votes']-$a['votes']); ?>
      <div class="bg-gray-50 rounded-xl p-4 shadow-md border border-gray-200">
        <h3 class="text-base font-bold text-gray-800 mb-3 border-b pb-2"><?= htmlspecialchars($position) ?></h3>
        <div class="h-32 mb-2"><canvas id="chart_<?= md5($etype.$position) ?>"></canvas></div>

        <!-- Candidate Table -->
        <input type="text" placeholder="Search..." class="w-full mb-2 px-2 py-1 border rounded search-input" data-table="table_<?= md5($etype.$position) ?>">
       <!-- Candidate Table -->
<table class="w-full text-xs border border-gray-200">
  <thead class="bg-blue-600 text-white">
    <tr>
      <th class="p-2 text-left">Candidate</th>
      <th class="p-2">Party</th>
      <th class="p-2">Votes</th>
      <th class="p-2">Standing</th>
      <th class="p-2 text-center">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($candidates as $i => $cand): ?>
      <tr class="border-b hover:bg-gray-50">
        <td class="p-2"><?= htmlspecialchars($cand['name']) ?></td>
        <td class="p-2"><?= htmlspecialchars($cand['party']) ?></td>
        <td class="p-2 font-semibold text-blue-700"><?= $cand['votes'] ?></td>
      <td class="p-2">
    <?php
        $prevVotes = $candidates[$i+1]['votes'] ?? 0;
        $gap = $cand['votes'] - $prevVotes;
if ($gap >= 10) {
    echo "<span class='text-green-600 font-semibold'>Leading</span>";
} elseif ($gap >= 3) {
    echo "<span class='text-blue-600 font-semibold'>Ahead</span>";
} else {
    echo "<span class='text-yellow-600 font-semibold'>Tight Race</span>";
}

    ?>
</td>

        <td class="p-2 text-center">
          <div class="flex justify-center gap-2 flex-wrap">
            <!-- View/Edit Button -->
            <button type="button"
               class="px-3 py-1 bg-blue-500 text-white text-[10px] rounded hover:bg-blue-600 transition w-full sm:w-auto"
               onclick="redirectToEdit('<?= addslashes($cand['name']) ?>', '<?= urlencode($etype) ?>', '<?= urlencode($position) ?>')">
               <i class="fas fa-cog"></i> Manage
            </button>

          <!-- Set Status Button -->
<!-- Set Status Button -->
<button type="button"
    class="px-3 py-1 bg-red-500 text-white text-[10px] rounded hover:bg-red-600 transition w-full sm:w-auto"
    onclick="openStatusModal(<?= json_encode($cand['student_id']) ?>, <?= json_encode($cand['name']) ?>)">
    <i class="fas fa-ban"></i> Set Status
</button>


<!-- Status Modal -->
<div id="status-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-80 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Set Candidate Status</h2>
        <p class="text-sm text-gray-700 mb-4">Candidate: <span id="status-candidate-name" class="font-medium"></span></p>
        <select id="status-select" class="w-full border rounded p-2 mb-4 text-sm">
            <option value="Active">Active</option>
            <option value="Suspended">Suspended</option>
            <option value="Disqualified">Disqualified</option>
        </select>
        <div class="flex justify-end gap-2">
            <button onclick="closeStatusModal()" class="px-3 py-1 text-sm rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
            <button onclick="submitStatusChange()" class="px-3 py-1 text-sm rounded bg-red-500 text-white hover:bg-red-600">Save</button>
        </div>
        <input type="hidden" id="status-candidate-id">
    </div>
</div>

          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- Centered Notification -->
<div id="notification" 
     class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 
            bg-gray-800 text-white px-6 py-3 rounded-xl shadow-lg 
            opacity-0 pointer-events-none transform scale-95 transition-all duration-300 z-50 text-center text-sm font-medium">
</div>

<script>
let notifTimeout;

// Show notification in center
function showNotification(message, type='info') {
    const notif = document.getElementById('notification');
    notif.textContent = message;

    switch(type){
        case 'success': notif.style.backgroundColor = '#16a34a'; break; // green
        case 'warning': notif.style.backgroundColor = '#eab308'; break; // yellow
        case 'danger':  notif.style.backgroundColor = '#dc2626'; break; // red
        default:        notif.style.backgroundColor = '#2563eb'; break; // blue
    }

    notif.style.pointerEvents = 'auto';
    notif.style.opacity = '1';
    notif.style.transform = 'translate(-50%, -50%) scale(1)';

    clearTimeout(notifTimeout);
    notifTimeout = setTimeout(()=>{
        notif.style.opacity = '0';
        notif.style.transform = 'translate(-50%, -50%) scale(0.95)';
        notif.style.pointerEvents = 'none';
    }, 2000);
}

// Redirect to manage_candidates.php for editing
function redirectToEdit(name, etype, position){
    showNotification(`Redirecting to edit ${name}...`, 'info');
    setTimeout(() => {
        window.location.href = `../admin/manage_candidates.php?id=${encodeURIComponent(name)}&etype=${encodeURIComponent(etype)}&position=${encodeURIComponent(position)}`;
    }, 500);
}

// Disqualify candidate
function disqualifyCandidate(name, etype, position){
    if(confirm(`Are you sure you want to disqualify ${name}?`)){
        showNotification(`${name} has been disqualified.`, 'danger');
        setTimeout(()=>{
            window.location.href = `disqualify_candidate.php?id=${encodeURIComponent(name)}&etype=${encodeURIComponent(etype)}&position=${encodeURIComponent(position)}`;
        }, 500);
    }
}
</script>


        <script>
          // Chart.js
          const labels_<?= md5($etype.$position) ?> = [];
          const votes_<?= md5($etype.$position) ?> = [];
          <?php foreach ($candidates as $cand): ?>
          labels_<?= md5($etype.$position) ?>.push("<?= addslashes($cand['name']) ?>");
          votes_<?= md5($etype.$position) ?>.push(<?= $cand['votes'] ?>);
          <?php endforeach; ?>
        new Chart(document.getElementById("chart_<?= md5($etype.$position) ?>"), {
    type: 'bar',
    data: { 
        labels: labels_<?= md5($etype.$position) ?>, 
        datasets: [{
            data: votes_<?= md5($etype.$position) ?>,
            backgroundColor: '#2563eb'
        }]
    },
    options: { 
        indexAxis: 'y', // <-- THIS MAKES IT HORIZONTAL
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { enabled: true }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { precision: 0 }
            },
            y: {
                ticks: { autoSkip: false } // <-- SHOW ALL NAMES
            }
        }
    }
});

        </script>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endforeach; ?>

<?php if(empty($all_election_results)): ?><div class="text-center py-6 text-red-500 text-sm">No election data available.</div><?php endif; ?>

</main>

<script>
// Auto Refresh
setInterval(()=>{ location.reload(); }, 60000);

// Simple table search filter
document.querySelectorAll('.search-input').forEach(input=>{
  input.addEventListener('input',()=>{
    const table = document.getElementById(input.dataset.table);
    const filter = input.value.toLowerCase();
    table.querySelectorAll('tbody tr').forEach(row=>{
      row.style.display = [...row.cells].some(td=>td.textContent.toLowerCase().includes(filter)) ? '' : 'none';
    });
  });
});

// CSV Export Function
function exportCSV(){
  let csv = 'Election,Position,Candidate,Party,Votes,Status\n';
  <?php foreach($orderedTypes as $etype): ?>
    <?php if(!isset($all_election_results[$etype])) continue; ?>
    <?php foreach($mergedPositions as $pos => $cands): ?>
      <?php foreach($cands as $c): ?>
        csv += '<?= addslashes($etype) ?>,<?= addslashes($pos) ?>,<?= addslashes($c['name']) ?>,<?= addslashes($c['party']) ?>,<?= $c['votes'] ?>,<?= ($c['votes']>=3)?'Leading':'Close' ?>\n';
      <?php endforeach; ?>
    <?php endforeach; ?>
  <?php endforeach; ?>
  const blob = new Blob([csv], { type: 'text/csv' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'election_results.csv';
  link.click();
}
</script>
<script>
function openStatusModal(studentId, candidateName) {
    document.getElementById('status-modal').classList.remove('hidden');
    document.getElementById('status-candidate-name').textContent = candidateName;
    document.getElementById('status-candidate-id').value = studentId;
}

function closeStatusModal() {
    document.getElementById('status-modal').classList.add('hidden');
}

function submitStatusChange() {
    const studentId = document.getElementById('status-candidate-id').value;
    const status = document.getElementById('status-select').value;

    // Optional: Add AJAX request to update status in backend
    fetch('update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ student_id: studentId, status: status })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        closeStatusModal();
        // Optional: reload page or update status in table dynamically
        location.reload();
    })
    .catch(err => {
        console.error(err);
        alert('Error updating status');
    });
}
</script>


<?php $conn->close(); ?>
</body>
</html>

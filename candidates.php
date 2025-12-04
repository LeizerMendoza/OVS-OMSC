<?php 
// candidates.php
include("database.php"); // must set $conn (mysqli)
// ORDER MAPPING
$orderMap = [
  'SSG' => 1, 'PADC' => 2, 'YMO' => 3, 'YES' => 4, 'LSC' => 5,
  'SPORTS CLUB' => 6, 'ENGLISH CLUB' => 7, 'SCI-MATH CLUB' => 8,
  'CYMA' => 9, 'UMSO' => 10, 'SAMFILKO' => 11
];
$positions = [
    'SSG' => ['Governor', 'Vice Governor', 'Board Member 1', 'Board Member 2', 'Board Member 3', 'Board Member 4', 'Board Member 5','Board Member 6', 'Board Member 7','Board Member 8', 'Board Member 9'],
    'PADC' => ['Mayor', 'Vice Mayor','Secretary', 'Treasurer','Auditor', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'YMO' => ['Mayor', 'Vice Mayor', 'Secretary', 'Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7', ],
    'YES' => ['Mayor', 'Vice Mayor', 'Secretary', 'Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7', ],
    'LSC' => ['Mayor', 'Vice Mayor', 'Secretary', 'Asst. Secretary','Treasurer','Asst. Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'SPORTS CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer', 'Asst. Treasurer','Auditor', 'PIO', 'Project Manager', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7', 'Councilor 8', 'Councilor 9', 'Councilor 10'],
    'ENGLISH CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer','Auditor', 'PIO', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'SCI-MATH CLUB' => ['President', 'Vice President', 'Secretary', 'Treasurer','Auditor', 'PIO','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'CYMA CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer','Auditor', 'PIO 1', 'PIO 2',  'Project Manager','Muse','Escort'],
    'UMSO CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary','Treasurer','Auditor', 'PIO', 'Project Manager'],
    'SAMFILKO' => ['Pangulo', 'Ikalawang Pangulo', 'Kalihim', 'Ingat Yaman','Taga-Suri', 'Tagapamayapa', 'Tagapangasiwang Proyekto'],
    'SECTION' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer', 'Asst. Treasurer','Auditor','PIO 1', 'PIO 2', 'Project Manager','Muse', 'Escort'],
];

// POSITION ORDER CASE (BASED ON YOUR PREDEFINED ARRANGEMENT)
$posCaseParts = [];

foreach ($positions as $etype => $posList) {
    $escapedType = mysqli_real_escape_string($conn, $etype);

    $fieldList = array_map(function($p) use ($conn) {
        return mysqli_real_escape_string($conn, $p);
    }, $posList);

    $posCaseParts[] =
        "WHEN election_type = '$escapedType' THEN FIELD(position, '" . implode("','", $fieldList) . "')";
}

$posCaseSql = "CASE " . implode(" ", $posCaseParts) . " ELSE 999 END";



// BUILD ORDER CASE
$caseParts = [];
foreach ($orderMap as $k => $v) {
  $caseParts[] = "WHEN election_type = '". mysqli_real_escape_string($conn, $k) ."' THEN $v";
}
$caseSql = "CASE " . implode(' ', $caseParts) . " ELSE 999 END";

$sql = "SELECT id, student_id, name, position, election_type, partylist, candidates, achievements
        FROM candidates
        ORDER BY 
            $caseSql,
            $posCaseSql,
            name ASC";


$result = mysqli_query($conn, $sql);

// ERROR CHECK
if (!$result) {
    die("SQL ERROR: " . mysqli_error($conn));
}

$candidates = [];
while ($r = mysqli_fetch_assoc($result)) {
    // Ensure achievements is an array
    if (is_string($r['achievements']) && !empty($r['achievements'])) {
        $decoded = json_decode($r['achievements'], true);
        $r['achievements'] = is_array($decoded) ? $decoded : [];
    } elseif (is_array($r['achievements'])) {
        // Already an array
        $r['achievements'] = $r['achievements'];
    } else {
        $r['achievements'] = [];
    }

    $candidates[] = $r;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Meet the Candidates | OMSC VoteSphere</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Satoshi&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body { font-family: 'Satoshi', sans-serif; }
    .h-scroller {
      overflow-x: auto;
      white-space: nowrap;
      scroll-snap-type: x mandatory;
      padding-bottom: 14px; gap: 12px;
    }
    .candidate-card {
      display: inline-block;
      width: 320px;
      scroll-snap-align: start;
      vertical-align: top;
    }
    .candidate-card img {
      height: 220px;
      object-fit: cover;
      border-radius: 12px;
    }
    .achievement-badge {
      background:#eef2ff; padding:6px 10px;
      border-radius:999px; margin:4px;
      display:inline-block; font-size:13px;
    }
    .viewer { display:none; }
    .viewer.active { display:flex; align-items:center; justify-content:center; }
    .viewer-card {
      width:95%; max-width:1100px; height:80vh;
      background:white; border-radius:12px;
      display:grid; grid-template-columns:1fr 1fr;
      overflow:hidden; box-shadow:0 10px 40px rgba(2,6,23,0.35);
      position:relative;
    }
    .viewer-left { background:#111827; display:flex; align-items:center; justify-content:center; }
    .viewer-left img { width:100%; height:100%; object-fit:cover; }
    .viewer-right { padding:28px; overflow:auto; }
    .viewer-controls { position:absolute; top:12px; left:12px; display:flex; gap:8px; }
    .viewer-controls button {
      background:white; border-radius:8px; padding:8px 12px;
    }
    .viewer-footer { position:absolute; bottom:12px; left:0; right:0; display:flex; justify-content:center; }
  </style>
</head>

<body class="flex flex-col min-h-screen">

<!-- NAVBAR -->
<nav class="bg-gradient-to-r from-[#0a2342] via-[#1e3a8a] to-[#3b82f6] fixed top-0 left-0 w-full z-50 shadow-xl transition-all duration-500 ease-in-out backdrop-blur-md">
  <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-20 text-white relative">
    <a href="student/welcome_home.php" class="flex items-center space-x-4">
      <img src="omsc-logo.png" alt="OMSC Logo" class="w-14 h-14 rounded-full shadow-lg border border-white/40">
      <div class="flex flex-col leading-tight">
        <h1 class="text-lg md:text-xl font-semibold tracking-wide">Occidental Mindoro State College</h1>
        <p class="text-xs md:text-sm text-blue-100 font-medium">OMSC VoteSphere — Student Election Portal</p>
      </div>
    </a>

    <ul class="hidden md:flex space-x-10 font-medium text-white items-center">
      <li>
        <a href="student/welcome_home.php" class="flex flex-col items-center group">
          <div class="bg-white/10 p-3 rounded-2xl group-hover:bg-white/20 transition-all duration-300 shadow-md">
            <i class="fas fa-house text-xl"></i>
          </div>
          <span class="text-xs mt-1 group-hover:text-blue-200 transition">Home</span>
        </a>
      </li>
      <li>
        <a href="student/election_type.php" class="flex flex-col items-center group">
          <div class="bg-white/10 p-3 rounded-2xl group-hover:bg-white/20 transition-all duration-300 shadow-md">
            <i class="fas fa-chart-bar text-xl"></i>
          </div>
          <span class="text-xs mt-1 group-hover:text-blue-200 transition">Vote</span>
        </a>
      </li>
      <li>
        <a href="candidates.php" class="flex flex-col items-center group">
          <div class="bg-white/10 p-3 rounded-2xl group-hover:bg-white/20 transition-all duration-300 shadow-md">
            <i class="fas fa-users text-xl"></i>
          </div>
          <span class="text-xs mt-1 group-hover:text-blue-200 transition">Candidates</span>
        </a>
      </li>
      <li>
        <button id="profile-btn-mobile" class="flex flex-col items-center hover:scale-110 transition-transform duration-300">
    <div class="bg-white/20 p-3 rounded-full shadow-lg ring-2 ring-blue-300/30">
      <i class="fas fa-user-circle text-3xl"></i>
    </div>
</button>
      </li>
    </ul>

    <button id="menu-btn" class="md:hidden text-white text-3xl focus:outline-none hover:text-blue-200">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="hidden fixed bottom-0 left-0 w-full bg-gradient-to-r from-[#0a2342] via-[#1e3a8a] to-[#3b82f6] flex justify-around py-3 text-white shadow-2xl rounded-t-3xl md:hidden">
    <a href="student/welcome_home.php" class="flex flex-col items-center group">
      <i class="fas fa-house text-xl group-hover:text-blue-300 transition"></i><span class="text-xs mt-1">Home</span>
    </a>
    <a href="student/election_type.php" class="flex flex-col items-center group">
      <i class="fas fa-chart-bar text-xl group-hover:text-blue-300 transition"></i><span class="text-xs mt-1">Vote</span>
    </a>
    <a href="student/candidates.php" class="flex flex-col items-center group">
      <i class="fas fa-users text-xl group-hover:text-blue-300 transition"></i><span class="text-xs mt-1">Candidates</span>
    </a>
    <a href="student/profile.php" class="flex flex-col items-center hover:scale-110 transition-transform duration-300">
      <i class="fas fa-user-circle text-3xl"></i>
    </a>
  </div>
</nav>

<script>
  const menuBtn = document.getElementById('menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  menuBtn?.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
    menuBtn.innerHTML = mobileMenu.classList.contains('hidden') ? '<i class="fas fa-bars"></i>' : '<i class="fas fa-times"></i>';
  });
</script>

<main class="pt-28 w-full flex-1">
  <div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-6">
      <h2 class="text-3xl md:text-4xl font-bold">Official List of Candidates</h2>
      <p class="text-sm text-gray-600 mt-2">Click any candidate to view full profile & achievements.</p>
    </div>
<?php
// GROUP CANDIDATES BY ELECTION TYPE AND THEN PARTYLIST
$grouped = [];
foreach ($candidates as $c) {
    $ptype = !empty($c['partylist']) ? $c['partylist'] : 'Independent';
    $grouped[$c['election_type']][$ptype][] = $c;
}
?>

<?php foreach ($orderMap as $etype => $ord): ?>
    <?php if (!isset($grouped[$etype])) continue; ?>

    <!-- ELECTION TYPE HEADER -->
    <h2 class="text-2xl font-bold mt-10 mb-3"><?= htmlspecialchars($etype) ?> Candidates</h2>

    <?php foreach ($grouped[$etype] as $party => $candidatesInParty): ?>
        <!-- PARTYLIST HEADER -->
        <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($party) ?></h3>

        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <div class="h-scroller">

<?php foreach ($candidatesInParty as $idx => $c): 
    // Image handling: use uploaded candidate photo or default
    $img = !empty($c['candidates']) ? 'uploads/candidates/' . htmlspecialchars($c['candidates']) : 'candidates/default-avatar.png';

    // Achievements
$achArr = [];

// If achievements is already an array (from previous fix)
if (is_array($c['achievements'])) {
    $achArr = $c['achievements'];
} 
// If achievements is a string, try JSON decode first
elseif (is_string($c['achievements']) && !empty($c['achievements'])) {
    $decoded = json_decode($c['achievements'], true);
    if (is_array($decoded)) {
        $achArr = $decoded;
    } else {
        // fallback: split by comma or newlines
        $tmp = preg_split("/\r\n|\n|\r|,/", $c['achievements']);
        $achArr = array_filter(array_map('trim', $tmp));
    }
}

?>

<div class="candidate-card bg-white rounded-xl shadow-md p-4 mr-3"
     data-index="<?= $idx ?>"
     data-id="<?= $c['id'] ?>"
     data-img="<?= htmlspecialchars($img) ?>"
     data-name="<?= htmlspecialchars($c['name']) ?>"
     data-position="<?= htmlspecialchars($c['position']) ?>"
     data-election="<?= htmlspecialchars($c['election_type']) ?>"
     data-party="<?= htmlspecialchars($c['partylist']) ?>"
     data-ach='<?= htmlspecialchars(json_encode($achArr), ENT_QUOTES) ?>'>

    <img src="<?= htmlspecialchars($img) ?>" class="w-full rounded-lg mb-3 cursor-pointer">
    <h3 class="text-lg font-semibold"><?= htmlspecialchars($c['name']) ?></h3>
    <p class="text-gray-600"><?= htmlspecialchars($c['position']) ?></p>

    <div class="mt-2 flex gap-2">
        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded"><?= htmlspecialchars($c['election_type']) ?></span>
        <?php if (!empty($c['partylist'])): ?>
            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded"><?= htmlspecialchars($c['partylist']) ?></span>
        <?php endif; ?>
    </div>

</div>
<?php endforeach; ?>

                
            </div>
        </div>

    <?php endforeach; ?>
<?php endforeach; ?>

  </div>
</main>
<!-- VIEWER -->
<div id="viewer" class="viewer fixed inset-0 bg-black bg-opacity-70 z-50 hidden items-center justify-center p-4">
  <div class="viewer-card relative bg-white rounded-xl overflow-hidden flex max-w-5xl w-full h-[80vh] shadow-2xl">

    <!-- LEFT IMAGE -->
    <div class="viewer-left relative flex-1 bg-gray-900 flex items-center justify-center">
      <img id="viewer-img" class="object-contain h-full w-full">

      <!-- PREV BUTTON -->
      <button id="prevBtn" class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white p-3 rounded-full shadow-lg z-50">
        <i class="fas fa-chevron-left text-xl text-gray-800"></i>
      </button>

      <!-- NEXT BUTTON -->
      <button id="nextBtn" class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white p-3 rounded-full shadow-lg z-50">
        <i class="fas fa-chevron-right text-xl text-gray-800"></i>
      </button>
    </div>

    <!-- RIGHT INFO -->
    <div class="viewer-right flex-1 p-6 overflow-auto relative">
      <h2 id="viewer-name" class="text-2xl font-bold mt-6"></h2>
      <p id="viewer-position" class="text-gray-700 mt-1"></p>

      <div class="mt-3 flex flex-wrap gap-2">
        <span id="viewer-election" class="text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded"></span>
        <span id="viewer-party" class="text-sm bg-green-100 text-green-800 px-3 py-1 rounded"></span>
      </div>

      <div id="viewer-achievements" class="mt-6">
        <h4 class="font-semibold">Achievements</h4>
        <div id="ach-list" class="mt-2"></div>
      </div>
    </div>

    <!-- EXIT BUTTON -->
    <button id="exitBtn" class="absolute top-3 right-3 bg-white/80 hover:bg-white p-2 rounded-full shadow-lg z-50">
      <i class="fas fa-times text-gray-800 text-lg"></i>
    </button>

  </div>
</div>

<script>
const cards = Array.from(document.querySelectorAll('.candidate-card'));
const viewer = document.getElementById("viewer");
const achList = document.getElementById('ach-list');

const viewerImg = document.getElementById('viewer-img');
const viewerName = document.getElementById('viewer-name');
const viewerPosition = document.getElementById('viewer-position');
const viewerElection = document.getElementById('viewer-election');
const viewerParty = document.getElementById('viewer-party');

let index = 0;

const data = cards.map(c => ({
  img: c.dataset.img,
  name: c.dataset.name,
  position: c.dataset.position,
  election: c.dataset.election,
  party: c.dataset.party,
  achievements: JSON.parse(c.dataset.ach)
}));

function openViewer(i){
  index=i;
  const d = data[i];

  viewerImg.src = d.img;
  viewerName.innerText = d.name;
  viewerPosition.innerText = d.position;
  viewerElection.innerText = d.election;

  if(d.party.trim()!==""){
    viewerParty.style.display="inline-block";
    viewerParty.innerText = d.party;
  } else {
    viewerParty.style.display="none";
  }

  achList.innerHTML = "";
  if(d.achievements.length){
    d.achievements.forEach(a=>{
      achList.innerHTML += `<span class="achievement-badge">${a}</span>`;
    });
  } else {
    achList.innerHTML = "<p class='text-gray-500'>No achievements listed.</p>";
  }

  // SHOW VIEWER
  viewer.classList.remove("hidden");
  viewer.classList.add("flex");
}

cards.forEach((c,i)=>{
  c.addEventListener("click",()=>openViewer(i));
});

document.getElementById("prevBtn").onclick = ()=>{
  index = (index-1 + data.length) % data.length;
  openViewer(index);
};

document.getElementById("nextBtn").onclick = ()=>{
  index = (index+1) % data.length;
  openViewer(index);
};

// EXIT BUTTON
document.getElementById("exitBtn").onclick = ()=>{
  viewer.classList.remove("flex");
  viewer.classList.add("hidden");
};

// CLOSE WHEN CLICKING OUTSIDE THE CARD
viewer.addEventListener("click", (e)=>{
  if(e.target === viewer){ // Only if the background itself is clicked
    viewer.classList.remove("flex");
    viewer.classList.add("hidden");
  }
});
</script>

<!-- FOOTER -->
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

</body>
</html>

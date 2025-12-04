<?php
session_start();
include '../database.php';

$student_id = $_SESSION['student_id'] ?? null;
$course = $_SESSION['course'] ?? '';
$club  = $_SESSION['club'] ?? [];
$section = $_SESSION['section'] ?? null;

// POSITION DISPLAY ORDER
$positions = [
    'SSG' => ['Governor', 'Vice Governor', 'Board Member 1', 'Board Member 2', 'Board Member 3', 'Board Member 4', 'Board Member 5','Board Member 6', 'Board Member 7','Board Member 8', 'Board Member 9'],
    'PADC' => ['Mayor', 'Vice Mayor','Secretary', 'Treasurer','Auditor', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'YMO' => ['Mayor', 'Vice Mayor', 'Secretary', 'Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'YES' => ['Mayor', 'Vice Mayor', 'Secretary', 'Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'LSC' => ['Mayor', 'Vice Mayor', 'Secretary', 'Asst. Secretary','Treasurer','Asst. Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'SPORTS CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer', 'Asst. Treasurer','Auditor', 'PIO', 'Project Manager', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7', 'Councilor 8', 'Councilor 9', 'Councilor 10'],
    'ENGLISH CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer','Auditor', 'PIO', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'SCI-MATH CLUB' => ['President', 'Vice President', 'Secretary', 'Treasurer','Auditor', 'PIO','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'CYMA' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer','Auditor', 'PIO 1', 'PIO 2', 'Project Manager','Muse','Escort'],
    'UMSO' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary','Treasurer','Auditor', 'PIO', 'Project Manager'],
    'SAMFILKO' => ['Pangulo', 'Ikalawang Pangulo', 'Kalihim', 'Ingat Yaman','Taga-Suri', 'Tagapamayapa', 'Tagapangasiwang Proyekto'],
    'SECTION' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer', 'Asst. Treasurer','Auditor','PIO 1', 'PIO 2', 'Project Manager','Muse', 'Escort'],
];

// Ensure $club is array
if (!is_array($club)) {
    $decoded = json_decode($club, true);
    if (is_array($decoded)) $club = $decoded;
    else $club = array_map('trim', explode(',', $club));
}

// Determine department election
$deptElection = null;
if ($course === 'BSIT') $deptElection = 'PADC';
elseif ($course === 'BEED') $deptElection = 'YMO';
elseif ($course === 'CBAM') $deptElection = 'YES';

// Election display order
$electionFlow = ['SSG'];
if ($deptElection) $electionFlow[] = $deptElection;
$electionFlow[] = 'LSC';

// Add clubs
if (!empty($club)) {
    $joinedClubs = [];
    foreach ($club as $c) {
        $clubUpper = strtoupper($c);
        if (isset($positions[$clubUpper])) $joinedClubs[] = $clubUpper;
    }
    $electionFlow = array_merge($electionFlow, $joinedClubs);
}

// Helper: fetch candidates per election type
function getCandidatesByElection($conn, $electionType) {
    $candidates = [];
    $stmt = $conn->prepare("SELECT id, name, position, partylist, candidates FROM candidates WHERE election_type = ? ORDER BY partylist, position, name");
    if (!$stmt) return $candidates;
    $stmt->bind_param("s", $electionType);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $candidates[] = $row;
    $stmt->close();
    return $candidates;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Voting | OMSC VoteSphere</title>
< <script src="https://cdn.tailwindcss.com"></script>
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

<div class="max-w-6xl mx-auto py-6 px-3">
  <h1 class="text-2xl font-bold text-center mb-6">Student Voting Page</h1>
  <div class="max-w-4xl mx-auto mb-6">
    <div class="flex justify-between mb-2 text-sm font-semibold text-green-700">
  <span id="stepText">Election 1</span>
  <span id="progressPercent">0%</span>
</div>

<div class="w-full bg-green-200 rounded-full h-3 overflow-hidden">
  <div id="progressBar" class="bg-green-600 h-3 rounded-full transition-all duration-500" style="width: 0%;"></div>
</div>

  </div>

  <form action="../student/submit_vote.php" method="POST" id="voteForm">

  <?php 
  $step = 0;
  foreach ($electionFlow as $etype): 
    $candidates = getCandidatesByElection($conn, $etype);
    if (count($candidates) === 0) continue;

    // Group by position
    $grouped = [];
    foreach ($candidates as $cand) $grouped[$cand['position']][] = $cand;

    // Separate normal & special positions
    $specialGroups = ['COUNCILORS'=>[], 'BOARD MEMBERS'=>[]];
    $normalGroups = [];
    foreach ($grouped as $pos => $cands) {
        if (stripos($pos,'Councilor')!==false) $specialGroups['COUNCILORS'] = array_merge($specialGroups['COUNCILORS'],$cands);
        elseif (stripos($pos,'Board Member')!==false) $specialGroups['BOARD MEMBERS'] = array_merge($specialGroups['BOARD MEMBERS'],$cands);
        else $normalGroups[$pos] = $cands;
    }
  ?>

  <div class="vote-step <?= $step===0 ? '' : 'hidden' ?>">
    <div class="bg-white shadow-lg rounded-xl p-6 mb-8">

      <h2 class="text-xl font-semibold mb-4 text-blue-700"><?= htmlspecialchars($etype) ?> Election</h2>
      <input type="hidden" name="election[]" value="<?= htmlspecialchars($etype) ?>">

      <!-- NORMAL POSITIONS -->
      <?php foreach($normalGroups as $position=>$cands): ?>
      <div class="mb-6 border-b pb-4">
        <h3 class="font-semibold text-lg mb-3"><?= htmlspecialchars($position) ?></h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <?php foreach($cands as $candidate): ?>
          <div class="border rounded-xl p-3 bg-white flex items-center gap-3">
            <?php if(!empty($candidate['candidates'])): ?>
              <img src="../uploads/candidates/<?= htmlspecialchars($candidate['candidates']) ?>" class="w-14 h-14 object-cover rounded-full border">
            <?php else: ?>
              <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 border">N/A</div>
            <?php endif; ?>
            <div class="flex-1">
              <h4 class="font-semibold"><?= htmlspecialchars($candidate['name']) ?></h4>
              <?php if(!empty($candidate['partylist'])): ?>
                <span class="inline-block mt-1 text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full"><?= htmlspecialchars($candidate['partylist']) ?></span>
              <?php endif; ?>
            </div>
            <input type="radio" name="vote[<?= $etype ?>][<?= $position ?>]" value="<?= (int)$candidate['id'] ?>" class="vote-input" data-position="<?= htmlspecialchars($position) ?>">
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- COUNCILORS -->
      <?php if(!empty($specialGroups['COUNCILORS'])): ?>
      <div class="mb-6 border-b pb-4">
        <h3 class="font-bold text-lg mb-3 text-blue-700">Councilors (Choose at least 1)</h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <?php foreach($specialGroups['COUNCILORS'] as $candidate): ?>
          <div class="border rounded-xl p-3 flex items-center gap-3">
            <?php if(!empty($candidate['candidates'])): ?>
              <img src="../uploads/candidates/<?= htmlspecialchars($candidate['candidates']) ?>" class="w-14 h-14 object-cover rounded-full border">
            <?php else: ?>
              <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 border">N/A</div>
            <?php endif; ?>
            <div class="flex-1"><?= htmlspecialchars($candidate['name']) ?></div>
            <input type="checkbox" name="vote[<?= $etype ?>][Councilors][]" value="<?= (int)$candidate['id'] ?>" class="vote-input councilor-group">
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- BOARD MEMBERS -->
      <?php if(!empty($specialGroups['BOARD MEMBERS'])): ?>
      <div class="mb-6 border-b pb-4">
        <h3 class="font-bold text-lg mb-3 text-blue-700">Board Members (Choose at least 1)</h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <?php foreach($specialGroups['BOARD MEMBERS'] as $candidate): ?>
          <div class="border rounded-xl p-3 flex items-center gap-3">
            <?php if(!empty($candidate['candidates'])): ?>
              <img src="../uploads/candidates/<?= htmlspecialchars($candidate['candidates']) ?>" class="w-14 h-14 object-cover rounded-full border">
            <?php else: ?>
              <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 border">N/A</div>
            <?php endif; ?>
            <div class="flex-1"><?= htmlspecialchars($candidate['name']) ?></div>
            <input type="checkbox" name="vote[<?= $etype ?>][BoardMembers][]" value="<?= (int)$candidate['id'] ?>" class="vote-input board-group">
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
  <?php $step++; endforeach; ?>

<!-- Navigation Buttons -->
<div class="flex justify-between items-center mt-6">

  <!-- Back Button -->
  <button type="button" id="prevBtn"
          class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg shadow hover:bg-gray-300 transition duration-300">
    Back
  </button>

  <!-- Submit Button (hidden until last step) -->
  <button type="submit" id="submitBtn"
          class="bg-green-600 text-white px-6 py-2 rounded-lg shadow hover:bg-green-700 transition duration-300 hidden">
    Submit All Votes
  </button>

  <!-- Next Button -->
  <button type="button" id="nextBtn"
          class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition duration-300">
    Next
  </button>

</div>



  </form>
</div>

<script>
// Step handling & progress (keep your original code)
const steps = document.querySelectorAll(".vote-step");
let currentStep = 0;

const nextBtn = document.getElementById("nextBtn");
const prevBtn = document.getElementById("prevBtn");
const submitBtn = document.getElementById("submitBtn");

const progressBar = document.getElementById("progressBar");
const progressPercent = document.getElementById("progressPercent");
const stepText = document.getElementById("stepText");

function showStep(index){
  steps.forEach((s,i)=>s.classList.toggle("hidden",i!==index));
  prevBtn.classList.toggle("hidden",index===0);
  nextBtn.classList.toggle("hidden",index===steps.length-1);
  submitBtn.classList.toggle("hidden",index!==steps.length-1);
  updateProgress();
}

function updateProgress(){
  const percent = Math.round(((currentStep+1)/steps.length)*100);
  progressBar.style.width = percent+"%";
  progressPercent.textContent = percent+"%";
  stepText.textContent = `Election ${currentStep+1} of ${steps.length}`;
}

// Validation for 1+ Councilors/Board Members & 1 per normal position
function validateCurrentStep(){
  const step = steps[currentStep];
  
  // Councilors: at least 1 if applicable
  const councilors = step.querySelectorAll(".councilor-group:checked").length;
  if(step.querySelectorAll(".councilor-group").length && councilors < 1){
    alert("❌ Select at least 1 Councilor!");
    return false;
  }

  // Board Members: at least 1 if applicable
  const board = step.querySelectorAll(".board-group:checked").length;
  if(step.querySelectorAll(".board-group").length && board < 1){
    alert("❌ Select at least 1 Board Member!");
    return false;
  }

  // Normal positions: exactly 1
  const radios = step.querySelectorAll("input[type='radio']");
  const positions = [...new Set([...radios].map(r=>r.dataset.position))];
  for(let pos of positions){
    if(step.querySelectorAll(`input[data-position="${pos}"]:checked`).length !== 1){
      alert(`❌ Select exactly 1 candidate for ${pos}`);
      return false;
    }
  }

  return true;
}


nextBtn.addEventListener("click",()=>{ if(!validateCurrentStep()) return; if(currentStep<steps.length-1){ currentStep++; showStep(currentStep); }});
prevBtn.addEventListener("click",()=>{ if(currentStep>0){ currentStep--; showStep(currentStep); }});
document.getElementById("voteForm").addEventListener("submit",function(e){ if(!validateCurrentStep()) e.preventDefault(); });
showStep(currentStep);
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
<?php include '../student/profile_data.php'; ?>
<?php include '../student/profile_overlay.php'; ?>
</body>
</html>

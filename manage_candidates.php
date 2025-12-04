<?php
session_start();
include '../database.php'; // should set $conn (mysqli)

// Ensure upload folder exists (server-side)
$uploadDir = __DIR__ . '/../uploads/candidates/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

// Positions & partylist arrays (kept as your original)
$positions = [
    'SSG' => ['Governor', 'Vice Governor', 'Board Member 1', 'Board Member 2', 'Board Member 3', 'Board Member 4', 'Board Member 5','Board Member 6', 'Board Member 7','Board Member 8', 'Board Member 9'],
    'PADC' => ['Mayor', 'Vice Mayor','Secretary', 'Treasurer','Auditor', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'YMO' => ['Mayor', 'Vice Mayor', 'Secretary', 'Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7', ],
    'YES' => ['Mayor', 'Vice Mayor', 'Secretary', 'Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7', ],
    'LSC' => ['Mayor', 'Vice Mayor', 'Secretary', 'Asst. Secretary','Treasurer','Asst. Treasurer','Auditor','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'SPORTS CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer', 'Asst. Treasurer','Auditor', 'PIO', 'Project Manager', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7', 'Councilor 8', 'Councilor 9', 'Councilor 10'],
    'ENGLISH CLUB' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer','Auditor', 'PIO', 'Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'SCI-MATH CLUB' => ['President', 'Vice President', 'Secretary', 'Treasurer','Auditor', 'PIO','Councilor 1', 'Councilor 2', 'Councilor 3', 'Councilor 4', 'Councilor 5', 'Councilor 6', 'Councilor 7'],
    'CYMA' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary', 'Treasurer','Auditor', 'PIO 1', 'PIO 2',  'Project Manager','Muse','Escort'],
    'UMSO' => ['President', 'Vice President', 'Secretary', 'Asst. Secretary','Treasurer','Auditor', 'PIO', 'Project Manager'],
    'SAMFILKO' => ['Pangulo', 'Ikalawang Pangulo', 'Kalihim', 'Ingat Yaman','Taga-Suri', 'Tagapamayapa', 'Tagapangasiwang Proyekto'],
];

$partylist_elections = ['SSG', 'PADC', 'YMO', 'YES', 'LSC'];

$message = "";

/**
 * Helper: sanitize filename
 */
function safeFilename($name) {
    $name = preg_replace('/[^A-Za-z0-9\-\_\.]/', '_', $name);
    return $name;
}

/**
 * HANDLE DELETE
 * URL: manage_candidates.php?action=delete&id=#
 */
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Get current photo filename
    $sel = $conn->prepare("SELECT candidates AS photo FROM candidates WHERE id = ?");
    $sel->bind_param("i", $id);
    $sel->execute();
    $res = $sel->get_result();

    if ($row = $res->fetch_assoc()) {
        if (!empty($row['photo'])) {
            $filePath = __DIR__ . '/../uploads/candidates/' . basename($row['photo']);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }
    $sel->close();

    // Delete DB row
    $del = $conn->prepare("DELETE FROM candidates WHERE id = ?");
    $del->bind_param("i", $id);

    if ($del->execute()) {
        $message = "<p class='text-green-600 text-center mb-4'>✅ Candidate deleted.</p>";
    } else {
        $message = "<p class='text-red-600 text-center mb-4'>❌ Could not delete candidate.</p>";
    }

    $del->close();
}


/**
 * HANDLE ADD
 * Form posts to same page with input name="action" value="add"
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $student_id = trim($_POST['student_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $election_type = trim($_POST['election_type'] ?? '');
    $partylist = in_array($election_type, $partylist_elections) ? trim($_POST['partylist'] ?? '') : null;
    // achievements come as JSON string client-side; either as JSON or as newline-joined
    $achievements_arr = $_POST['achievements'] ?? [];
    if (!is_array($achievements_arr)) $achievements_arr = [];
    // filter empties
    $achievements_arr = array_values(array_filter(array_map('trim', $achievements_arr), function($v){ return $v !== ''; }));
    $achievements_json = json_encode($achievements_arr, JSON_UNESCAPED_UNICODE);

    // Handle file upload (optional)
    $photoFilename = null;
    if (!empty($_FILES['photo']['name'])) {
        $file = $_FILES['photo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        if (in_array($ext, $allowed) && $file['size'] <= 4 * 1024 * 1024) {
            $basename = time() . '_' . safeFilename(pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $ext;
            $dest = $uploadDir . $basename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $photoFilename = $basename;
            }
        } else {
            $message = "<p class='text-red-600 text-center mb-4'>❌ Invalid image or too large (max 4MB).</p>";
        }
    }

    // require required fields
    if ($name === '' || $position === '' || $election_type === '') {
        $message = "<p class='text-red-600 text-center mb-4'>⚠️ Name, position and election type are required.</p>";
    } else {
        // Insert (note: 'candidates' column holds the file name/path)
        $query = "INSERT INTO candidates (student_id, name, position, election_type, partylist, candidates, achievements) VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("sssssss", $student_id, $name, $position, $election_type, $partylist, $photoFilename, $achievements_json);
            if ($stmt->execute()) {
                $message = "<p class='text-green-600 text-center mb-4'>✅ Candidate added successfully!</p>";
            } else {
                $message = "<p class='text-red-600 text-center mb-4'>❌ DB error: " . htmlspecialchars($stmt->error) . "</p>";
            }
            $stmt->close();
        } else {
            $message = "<p class='text-red-600 text-center mb-4'>❌ DB prepare error: " . htmlspecialchars($conn->error) . "</p>";
        }
    }
}

/**
 * HANDLE START EDIT: load record into $editCandidate if action=edit&id=#
 */
$editCandidate = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sel = $conn->prepare("SELECT id, student_id, name, position, election_type, partylist, candidates, achievements FROM candidates WHERE id = ?");
    $sel->bind_param("i", $id);
    $sel->execute();
    $res = $sel->get_result();
    $editCandidate = $res->fetch_assoc() ?? null;
    $sel->close();
}

/**
 * HANDLE UPDATE (POST action=update)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $student_id = trim($_POST['student_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $election_type = trim($_POST['election_type'] ?? '');
    $partylist = in_array($election_type, $partylist_elections) ? trim($_POST['partylist'] ?? '') : null;
    $achievements_arr = $_POST['achievements'] ?? [];
    if (!is_array($achievements_arr)) $achievements_arr = [];
    $achievements_arr = array_values(array_filter(array_map('trim', $achievements_arr), function($v){ return $v !== ''; }));
    $achievements_json = json_encode($achievements_arr, JSON_UNESCAPED_UNICODE);

    // Optionally handle new photo upload
    $photoFilename = null;
    if (!empty($_FILES['photo']['name'])) {
        $file = $_FILES['photo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        if (in_array($ext, $allowed) && $file['size'] <= 4 * 1024 * 1024) {
            $basename = time() . '_' . safeFilename(pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $ext;
            $dest = $uploadDir . $basename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $photoFilename = $basename;
                // delete old file if exists
                $sel = $conn->prepare("SELECT candidates FROM candidates WHERE id = ?");
                $sel->bind_param("i", $id);
                $sel->execute();
                $r = $sel->get_result()->fetch_assoc();
                if ($r && !empty($r['candidates'])) {
                    $old = __DIR__ . '/../uploads/candidates/' . basename($r['candidates']);
                    if (file_exists($old)) @unlink($old);
                }
                $sel->close();
            }
        } else {
            $message = "<p class='text-red-600 text-center mb-4'>❌ Invalid image or too large (max 4MB).</p>";
        }
    }

    // Build update SQL depending on whether photo changed
    if ($photoFilename !== null) {
        $qry = "UPDATE candidates SET student_id = ?, name = ?, position = ?, election_type = ?, partylist = ?, candidates = ?, achievements = ? WHERE id = ?";
        if ($stmt = $conn->prepare($qry)) {
            $stmt->bind_param("sssssssi", $student_id, $name, $position, $election_type, $partylist, $photoFilename, $achievements_json, $id);
            if ($stmt->execute()) {
                $message = "<p class='text-green-600 text-center mb-4'>✅ Candidate updated successfully.</p>";
            } else {
                $message = "<p class='text-red-600 text-center mb-4'>❌ Update error: " . htmlspecialchars($stmt->error) . "</p>";
            }
            $stmt->close();
        }
    } else {
        $qry = "UPDATE candidates SET student_id = ?, name = ?, position = ?, election_type = ?, partylist = ?, achievements = ? WHERE id = ?";
        if ($stmt = $conn->prepare($qry)) {
            $stmt->bind_param("ssssssi", $student_id, $name, $position, $election_type, $partylist, $achievements_json, $id);
            if ($stmt->execute()) {
                $message = "<p class='text-green-600 text-center mb-4'>✅ Candidate updated successfully.</p>";
            } else {
                $message = "<p class='text-red-600 text-center mb-4'>❌ Update error: " . htmlspecialchars($stmt->error) . "</p>";
            }
            $stmt->close();
        }
    }

    // reload editCandidate for UI
    $sel = $conn->prepare("SELECT id, student_id, name, position, election_type, partylist, candidates, achievements FROM candidates WHERE id = ?");
    $sel->bind_param("i", $id);
    $sel->execute();
    $editCandidate = $sel->get_result()->fetch_assoc();
    $sel->close();
}

/**
 * FETCH candidates list for table/grid display
 */
// Build SQL ORDER BY CASE for all election types
$orderCases = [];
foreach ($positions as $etype => $posArr) {
    $posList = implode("','", $posArr); // e.g. 'Mayor','Vice Mayor','Secretary',...
    $orderCases[] = "WHEN election_type = '". $etype ."' THEN FIELD(position,'". $posList ."')";
}
$orderByCase = "CASE " . implode(" ", $orderCases) . " ELSE 0 END";

// Fetch candidates list ordered by election type and custom positions
$candidatesList = [];
$q = "SELECT id, student_id, name, position, election_type, partylist, candidates, achievements FROM candidates ORDER BY election_type, position, name";
if ($res = $conn->query($q)) {
    while ($row = $res->fetch_assoc()) {
        $candidatesList[] = $row;
    }
    $res->free();
}

// Group candidates by election type
$groupedCandidates = [];
foreach ($candidatesList as $c) {
    $groupedCandidates[$c['election_type']][] = $c;
}


?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Manage Candidates | OMSC VoteSphere</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* small utility for horizontal image scroller */
    .h-scroll { overflow-x:auto; -webkit-overflow-scrolling: touch; white-space: nowrap; padding-bottom: 8px; }
    .h-scroll img { display:inline-block; margin-right:8px; vertical-align:middle; height:110px; border-radius:8px; object-fit:cover; }
    .achievement-badge { background:#eef2ff; padding:6px 10px; border-radius:999px; margin:4px; display:inline-block; font-size:13px; }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

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

    <!-- MAIN -->
    <main class="flex-1 md:ml-64 p-6">

    
     <div class="max-w-7xl mx-auto">
  <!-- HEADER WRAPPER -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">

    <!-- TITLE + SUBTEXT -->
    <div>
      <h2 class="text-3xl font-bold text-[#0a2342] tracking-tight">
        Manage Candidates
      </h2>
      <p class="text-sm text-gray-500 mt-1 leading-relaxed">
        Maintain candidate profiles, update details, upload photos, and manage achievements efficiently.
      </p>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="flex items-center gap-3">
      <!-- ADD CANDIDATE BUTTON -->
      <button id="show-add"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-medium shadow-sm hover:bg-green-700 transition-all duration-200">
        <i class="fas fa-plus"></i>
        Add Candidate
      </button>

    </div>
  </div>
</div>

        <!-- feedback -->
        <?= $message ?>

        <!-- ADD / EDIT FORM (toggle) -->
        <div id="form-area" class="bg-white rounded-xl shadow p-6 mb-6 hidden">
          <form id="candidateForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="form-action" value="add">
            <input type="hidden" name="id" id="candidate-id" value="">
            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Candidate Name</label>
                <input type="text" name="name" id="name" class="w-full px-3 py-2 border rounded" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Student ID</label>
                <input type="text" name="student_id" id="student_id" class="w-full px-3 py-2 border rounded">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Election Type</label>
                <select name="election_type" id="election_type" class="w-full px-3 py-2 border rounded" required>
                  <option value="">Choose an Election Type</option>
                  <?php foreach ($positions as $t => $pl): ?>
                    <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div id="partylistWrap" class="hidden">
                <label class="block text-sm font-medium text-gray-700">Partylist (if applicable)</label>
                <input type="text" name="partylist" id="partylist" class="w-full px-3 py-2 border rounded">
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Position</label>
                <select name="position" id="position" class="w-full px-3 py-2 border rounded" required>
                  <option value="">Select position</option>
                </select>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Photo (optional, max 4MB)</label>
                <input type="file" name="photo" id="photo" accept="image/*" class="w-full">
                <div class="mt-3 h-scroll" id="photoPreviewArea"></div>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Achievements</label>
                <div id="achievementsContainer" class="space-y-2">
                  <!-- dynamic achievement inputs -->
                  <div class="flex gap-2">
                    <input type="text" name="achievements[]" class="flex-1 px-3 py-2 border rounded" placeholder="Achievement (e.g. '1st Prize — Math Olympiad')">
                    <button type="button" class="remove-ach-btn px-3 py-2 bg-red-100 rounded hidden"><i class="fas fa-trash text-red-600"></i></button>
                  </div>
                </div>
                <div class="mt-3">
                  <button type="button" id="addAchievementBtn" class="px-3 py-2 bg-blue-100 rounded text-sm"><i class="fas fa-plus mr-2"></i> Add achievement</button>
                </div>
              </div>
            </div>

            <div class="mt-4 flex gap-3">
              <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded" id="submitBtn"><i class="fas fa-save mr-2"></i> Save</button>
              <button type="button" class="bg-gray-100 px-4 py-2 rounded" id="cancelBtn">Cancel</button>
            </div>
          </form>
        </div>

      <div class="bg-white rounded-xl shadow p-4 mb-6">
  <h3 class="font-medium text-gray-700 mb-3">Candidate Photos</h3>
  <?php foreach ($positions as $etype => $posArr): ?>
    <?php if (!empty($groupedCandidates[$etype])): ?>
      <h4 class="text-sm font-semibold text-gray-600 mt-3 mb-2"><?= htmlspecialchars($etype) ?></h4>
      <div class="h-scroll py-2">
        <?php foreach ($posArr as $pos): ?>
          <?php foreach ($groupedCandidates[$etype] as $c): ?>
            <?php if ($c['position'] === $pos): 
                $img = !empty($c['candidates']) ? '../uploads/candidates/' . htmlspecialchars($c['candidates']) : '../omsc-logo.png';
            ?>
              <div class="inline-block mr-3">
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($c['name']) ?>" class="h-28 w-28 object-cover rounded-lg border cursor-pointer" title="<?= htmlspecialchars($c['name']) ?>" onclick="openPreview(<?= $c['id'] ?>)">
                <div class="text-xs mt-1 text-center"><?= htmlspecialchars($c['name']) ?></div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>


        <!-- SEARCH + TABLE -->
        <div class="bg-white rounded-xl shadow p-4">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <input id="searchBox" type="text" placeholder="Search candidates, student ID, position, partylist..." class="px-3 py-2 border rounded w-96">
            </div>
            <div class="text-sm text-gray-500">Showing <strong><?= count($candidatesList) ?></strong> candidates</div>
          </div>
<div class="overflow-x-auto">
  <table class="min-w-full divide-y">
    <thead class="bg-gray-50 text-left">
      <tr>
        <th class="px-4 py-2 text-sm font-medium text-gray-600">#</th>
        <th class="px-4 py-2 text-sm font-medium text-gray-600">Photo</th>
        <th class="px-4 py-2 text-sm font-medium text-gray-600">Student ID</th>
        <th class="px-4 py-2 text-sm font-medium text-gray-600">Name</th>
        <th class="px-4 py-2 text-sm font-medium text-gray-600">Position</th>
        <th class="px-4 py-2 text-sm font-medium text-gray-600">Election Type</th>
        <th class="px-4 py-2 text-sm font-medium text-gray-600">Partylist</th>
        <th class="px-4 py-2 text-sm font-medium text-gray-600">Achievements</th>
        <th class="px-4 py-2 text-sm font-medium text-gray-600">Actions</th>
      </tr>
    </thead>
    <tbody id="candidatesTable" class="divide-y">
      <?php $i=1; ?>
      <?php foreach ($positions as $etype => $posArr): ?>
        <?php if (!empty($groupedCandidates[$etype])): ?>
          <tr class="bg-gray-100 font-semibold"><td colspan="9" class="px-4 py-2"><?= htmlspecialchars($etype) ?></td></tr>
          <?php foreach ($posArr as $pos): ?>
            <?php foreach ($groupedCandidates[$etype] as $c): ?>
              <?php if ($c['position'] === $pos): 
                  $img = !empty($c['candidates']) ? '../uploads/candidates/' . htmlspecialchars($c['candidates']) : '../omsc-logo.png';
                  $ach_arr = [];
                  if (!empty($c['achievements'])) $ach_arr = json_decode($c['achievements'], true);
              ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-3 text-sm"><?= $i++ ?></td>
                  <td class="px-4 py-3 text-sm"><img src="<?= $img ?>" alt="photo" class="h-12 w-12 object-cover rounded"></td>
                  <td class="px-4 py-3 text-sm"><?= htmlspecialchars($c['student_id']) ?></td>
                  <td class="px-4 py-3 text-sm font-medium"><?= htmlspecialchars($c['name']) ?></td>
                  <td class="px-4 py-3 text-sm"><?= htmlspecialchars($c['position']) ?></td>
                  <td class="px-4 py-3 text-sm"><?= htmlspecialchars($c['election_type']) ?></td>
                  <td class="px-4 py-3 text-sm"><?= htmlspecialchars($c['partylist']) ?></td>
                  <td class="px-4 py-3 text-sm">
                    <?php foreach ($ach_arr as $ach): ?>
                      <span class="achievement-badge"><?= htmlspecialchars($ach) ?></span>
                    <?php endforeach; ?>
                  </td>
<td class="px-4 py-3 text-sm flex gap-2">
  <!-- Edit Button -->
  <a href="?action=edit&id=<?= $c['id'] ?>"
     class="flex items-center gap-1 px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-md hover:bg-yellow-200 transition w-full">
     <i class="fas fa-edit"></i> Edit
  </a>

  <!-- Delete Button -->
  <a href="?action=delete&id=<?= $c['id'] ?>" 
     onclick="return confirm('Delete candidate <?= htmlspecialchars($c['name']) ?>? This cannot be undone.')"
         class="flex items-center gap-1 px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 transition w-full">
     <i class="fas fa-trash"></i> Delete
  </a>
</td>


                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

        </div>

      </div>
    </main>
  </div>

<!-- Preview / Modal for Admin -->
<div id="adminPreview" class="fixed inset-0 bg-black bg-opacity-70 z-50 hidden items-center justify-center p-4">
  <div class="bg-white rounded-lg p-6 w-full max-w-3xl relative">
    <button onclick="closeAdminPreview()" class="absolute top-4 right-4 text-gray-600"><i class="fas fa-times text-2xl"></i></button>
    <div id="adminPreviewContent"></div>
  </div>
</div>

<script>
  // UI wiring
  const showAddBtn = document.getElementById('show-add');
  const formArea = document.getElementById('form-area');
  const candidateForm = document.getElementById('candidateForm');
  const cancelBtn = document.getElementById('cancelBtn');
  const formAction = document.getElementById('form-action');
  const candidateIdInput = document.getElementById('candidate-id');
  const photoPreviewArea = document.getElementById('photoPreviewArea');
  const achievementsContainer = document.getElementById('achievementsContainer');
  const addAchievementBtn = document.getElementById('addAchievementBtn');
  const positionSelect = document.getElementById('position');
  const electionTypeSelect = document.getElementById('election_type');
  const partylistWrap = document.getElementById('partylistWrap');

  // pre-fill positions list based on selected election type
  const positions = <?= json_encode($positions) ?>;
  const partylist_elections = <?= json_encode($partylist_elections) ?>;

  function populatePositions(type, selected = '') {
    positionSelect.innerHTML = '<option value="">Select position</option>';
    if (positions[type]) {
      positions[type].forEach(p => {
        const opt = document.createElement('option');
        opt.value = p;
        opt.innerText = p;
        if (p === selected) opt.selected = true;
        positionSelect.appendChild(opt);
      });
    }
  }

  electionTypeSelect?.addEventListener('change', () => {
    const v = electionTypeSelect.value;
    populatePositions(v);
    // show partylist input if this is a partylist election
    if (partylist_elections.includes(v)) {
      partylistWrap.classList.remove('hidden');
    } else {
      partylistWrap.classList.add('hidden');
    }
  });

  showAddBtn?.addEventListener('click', () => {
    formAction.value = 'add';
    candidateIdInput.value = '';
    candidateForm.reset();
    // clear achievements and add one blank
    achievementsContainer.innerHTML = '';
    addAchievementInput('');
    photoPreviewArea.innerHTML = '';
    formArea.classList.toggle('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  cancelBtn?.addEventListener('click', () => {
    formArea.classList.add('hidden');
  });

  // dynamic achievements
  function addAchievementInput(val = '') {
    const wrapper = document.createElement('div');
    wrapper.className = 'flex gap-2';
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'achievements[]';
    input.className = 'flex-1 px-3 py-2 border rounded';
    input.placeholder = 'Achievement (e.g. 1st Prize — Math Olympiad)';
    input.value = val;
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'remove-ach-btn px-3 py-2 bg-red-100 rounded';
    removeBtn.innerHTML = '<i class="fas fa-trash text-red-600"></i>';
    removeBtn.addEventListener('click', () => wrapper.remove());
    wrapper.appendChild(input);
    wrapper.appendChild(removeBtn);
    achievementsContainer.appendChild(wrapper);
  }

  addAchievementBtn?.addEventListener('click', () => addAchievementInput(''));

  // if edit link was clicked, server provided ?action=edit&id= and editCandidate was loaded
  <?php if ($editCandidate): 
      $ed = $editCandidate;
      $edit_json_ach = $ed['achievements'] ? json_encode(json_decode($ed['achievements'], true)) : '[]';
  ?>
    // populate edit data
    formArea.classList.remove('hidden');
    formAction.value = 'update';
    candidateIdInput.value = <?= json_encode($ed['id']) ?>;
    document.getElementById('name').value = <?= json_encode($ed['name']) ?>;
    document.getElementById('student_id').value = <?= json_encode($ed['student_id']) ?>;
    document.getElementById('election_type').value = <?= json_encode($ed['election_type']) ?>;
    populatePositions(<?= json_encode($ed['election_type']) ?>, <?= json_encode($ed['position']) ?>);
    document.getElementById('partylist').value = <?= json_encode($ed['partylist']) ?>;
    // achievements
    achievementsContainer.innerHTML = '';
    const edAch = <?= $edit_json_ach ?>;
    if (Array.isArray(edAch) && edAch.length) {
      edAch.forEach(a => addAchievementInput(a));
    } else {
      addAchievementInput('');
    }
    // preview photo if exists
    const existingPhoto = <?= json_encode($ed['candidates']) ?>;
    if (existingPhoto) {
      photoPreviewArea.innerHTML = '<img src=\"../uploads/candidates/' + existingPhoto + '\" class=\"h-28 object-cover rounded\">';
    } else {
      photoPreviewArea.innerHTML = '';
    }
  <?php endif; ?>

  // client-side preview of selected photo
  document.getElementById('photo')?.addEventListener('change', function(e){
    const f = this.files[0];
    if (!f) return;
    const url = URL.createObjectURL(f);
    photoPreviewArea.innerHTML = '<img src="'+url+'" class="h-28 object-cover rounded">';
  });

  // client-side search
  document.getElementById('searchBox')?.addEventListener('input', function(e){
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('#candidatesTable tr').forEach(row => {
      row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  // admin preview modal
  function openPreview(id) {
    // locate the candidate data in PHP-provided JS list
    const cands = <?= json_encode($candidatesList) ?>;
    const c = cands.find(x => parseInt(x.id) === parseInt(id));
    if (!c) return;
    const container = document.getElementById('adminPreviewContent');
    const imgUrl = c.candidates ? '../uploads/candidates/' + c.candidates : '../omsc-logo.png';
    let achHtml = '';
    try {
      const arr = c.achievements ? JSON.parse(c.achievements) : [];
      if (Array.isArray(arr) && arr.length) {
        achHtml = '<div class=\"mt-3\"><h4 class=\"font-medium\">Achievements</h4><div class=\"mt-2\">';
        arr.forEach(a => achHtml += '<span class=\"achievement-badge\">' + escapeHtml(a) + '</span>');
        achHtml += '</div></div>';
      }
    } catch (err) { /* ignore */ }

    container.innerHTML = `
      <div class="grid md:grid-cols-2 gap-4">
        <div><img src="${imgUrl}" class="w-full h-72 object-cover rounded"></div>
        <div>
          <h3 class="text-xl font-semibold">${escapeHtml(c.name || '')}</h3>
          <p class="text-sm text-gray-600 mt-1">Student ID: ${escapeHtml(c.student_id || '')}</p>
          <p class="text-sm text-gray-600 mt-1">Position: ${escapeHtml(c.position || '')}</p>
          <p class="text-sm text-gray-600 mt-1">Election Type: ${escapeHtml(c.election_type || '')}</p>
          <p class="text-sm text-gray-600 mt-1">Partylist: ${escapeHtml(c.partylist || '')}</p>
          ${achHtml}
        </div>
      </div>
    `;
    document.getElementById('adminPreview').classList.remove('hidden');
  }

  function closeAdminPreview() {
    document.getElementById('adminPreview').classList.add('hidden');
  }

  function escapeHtml(s) {
    if (!s) return '';
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // ensure remove button only shows when more than one
  document.addEventListener('click', e => {
    document.querySelectorAll('.remove-ach-btn').forEach(btn => {
      btn.classList.toggle('hidden', document.querySelectorAll('#achievementsContainer > .flex').length <= 1);
    });
  });

</script>
</body>
</html>

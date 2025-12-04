<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../database.php'; // database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: ../signin.php");
    exit();
}

$student_id = (int) $_SESSION['user_id']; // user ID from session
$messages = [];

// --- Fetch user info ---
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res) ?: [];
mysqli_stmt_close($stmt);

// Example notifications
$notifications = [['title'=>'Welcome','message'=>'Check latest election updates.']];
$notif_count = count($notifications);

// --- Handle avatar upload ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload_avatar') {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['avatar'];
        $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
        $maxBytes = 2 * 1024 * 1024;

        if ($file['error'] === UPLOAD_ERR_OK) {
            if ($file['size'] > $maxBytes) {
                $messages[] = ['type'=>'error','text'=>'Image too large (max 2MB).'];
            } elseif (!in_array(mime_content_type($file['tmp_name']), $allowed, true)) {
                $messages[] = ['type'=>'error','text'=>'Invalid image type.'];
            } else {
                $uploadDir = __DIR__ . '/../profile_pics/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newName = 'avatar_u' . $student_id . '_' . time() . '.' . $ext;
                $dest = $uploadDir . $newName;

                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $relativePath = '../profile_pics/' . $newName;
                    $stmt = mysqli_prepare($conn, "UPDATE users SET avatar=? WHERE id=?");
                    mysqli_stmt_bind_param($stmt, "si", $relativePath, $student_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $messages[] = ['type'=>'success','text'=>'Profile photo updated.'];
                    $user['avatar'] = $relativePath; // update displayed avatar
                } else {
                    $messages[] = ['type'=>'error','text'=>'Failed to upload file.'];
                }
            }
        }
    }
}

// --- Handle profile edit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit_profile') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact_no = trim($_POST['contact_no'] ?? '');
    $section = trim($_POST['section'] ?? '');
    $year_level = trim($_POST['year_level'] ?? '');

    if ($fullname && $email) {
        $stmt = mysqli_prepare($conn, "UPDATE users SET fullname=?, email=?, contact_no=?, section=?, year_level=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssssi", $fullname, $email, $contact_no, $section, $year_level, $student_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $messages[] = ['type'=>'success','text'=>'Profile updated successfully.'];

        // Update $user array to reflect changes immediately
        $user['fullname'] = $fullname;
        $user['email'] = $email;
        $user['contact_no'] = $contact_no;
        $user['section'] = $section;
        $user['year_level'] = $year_level;
    } else {
        $messages[] = ['type'=>'error','text'=>'Full name and email are required.'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profile | OMSC VoteSphere</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
  /* Overlay and dim background */
  #dim-bg { position: fixed; inset:0; background: rgba(0,0,0,0.45); opacity:0; pointer-events:none; transition:0.3s; z-index:40; }
  #dim-bg.active { opacity:1; pointer-events:auto; }

  #profile-overlay { position: fixed; top:0; right:0; width:400px; max-width:95%; height:100%; background:#fff; box-shadow:-4px 0 20px rgba(0,0,0,0.2); transform:translateX(100%); transition:0.4s; z-index:50; overflow:auto; padding:1.5rem;}
  #profile-overlay.active { transform:translateX(0); }

  .profile-avatar { width:96px; height:96px; object-fit:cover; border-radius:9999px; border:2px solid #3b82f6; }
  .badge-count { position:absolute; top:-5px; right:-5px; background:#ef4444; color:white; font-size:11px; padding:2px 6px; border-radius:999px; }
</style>
</head>
<body class="bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 min-h-screen text-gray-800">

<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 z-30 bg-gradient-to-r from-[#0a2342] via-[#1e3a8a] to-[#3b82f6] shadow-lg backdrop-blur-md">
  <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center text-white">
    <a href="../student/welcome_home.php" class="flex items-center gap-3">
      <img src="../omsc-logo.png" class="w-12 h-12 rounded-full border border-white/30">
      <span class="font-semibold">OMSC VoteSphere</span>
    </a>
    <div class="flex items-center gap-4">
      <button id="profile-btn" class="relative text-2xl"><i class="fas fa-user-circle"></i>
        <?php if($notif_count>0): ?><span class="badge-count"><?=$notif_count?></span><?php endif; ?>
      </button>
    </div>
  </div>
</nav>

<!-- Dim background -->
<div id="dim-bg"></div>
<!-- PROFILE OVERLAY -->
<!-- PROFILE OVERLAY -->
<aside id="profile-overlay"
       class="fixed top-0 right-0 w-96 max-w-md h-full bg-gradient-to-b from-blue-100 to-blue-300 shadow-xl translate-x-full transition-transform z-50 overflow-auto text-black font-sans">

    <!-- HEADER -->
    <div class="border-b pb-3 px-4 pt-4">
        <h2 class="text-xl font-bold text-gray-900 leading-tight">
            <?= htmlspecialchars($user['fullname'] ?? 'Student') ?>
        </h2>
        <button id="close-profile" class="absolute right-4 top-4 text-gray-600 hover:text-black">
            <i class="fas fa-times text-base"></i>
        </button>
    </div>

    <!-- PROFILE SECTION -->
    <div class="mt-4 mx-4 bg-white bg-opacity-90 rounded-lg border border-gray-200 shadow-sm p-3">
        <h3 class="text-sm font-semibold text-gray-800 border-b pb-1 mb-2">Profile Information</h3>

        <dl class="text-xs space-y-1 leading-relaxed text-gray-700">
            <div><dt class="font-medium">Student ID:</dt> <dd><?= htmlspecialchars($user['student_id'] ?? '-') ?></dd></div>
            <div><dt class="font-medium">Full Name:</dt> <dd><?= htmlspecialchars($user['fullname'] ?? '-') ?></dd></div>
            <div><dt class="font-medium">Email:</dt> <dd><?= htmlspecialchars($user['email'] ?? '-') ?></dd></div>
            <div><dt class="font-medium">Section:</dt> <dd><?= htmlspecialchars($user['section'] ?? '-') ?></dd></div>
            <div><dt class="font-medium">Course:</dt> <dd><?= htmlspecialchars($user['course'] ?? '-') ?></dd></div>
            <div><dt class="font-medium">Contact:</dt> <dd><?= htmlspecialchars($user['contact_no'] ?? '-') ?></dd></div>
        </dl>
    </div>

    <!-- VOTING STATUS -->
    <div class="mt-4 mx-4 bg-white bg-opacity-90 rounded-lg border border-gray-200 shadow-sm p-3">
        <h3 class="text-sm font-semibold text-gray-800 border-b pb-1 mb-2">Voting Status</h3>

        <div class="grid grid-cols-2 gap-2 text-center">
            <div class="p-2 bg-gray-50 border border-gray-200 rounded-md">
                <p class="text-xs text-gray-600">Has Voted</p>
                <p class="text-sm font-semibold text-gray-800"><?= ($user['has_voted'] ?? 0) ? 'Yes' : 'No' ?></p>
            </div>
            <div class="p-2 bg-gray-50 border border-gray-200 rounded-md">
                <p class="text-xs text-gray-600">Total Elections</p>
                <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($user['total_elections'] ?? 0) ?></p>
            </div>
        </div>

        <!-- REVIEW BUTTON -->
        <div class="mt-3">
            <?php if (!empty($user['has_voted']) && $user['has_voted']): ?>
                <a href="../student/review_vote.php?user_id=<?= $student_id ?>"
                   class="block text-center border border-gray-600 text-black px-3 py-1 rounded-md text-xs hover:bg-gray-100 transition">
                    Review My Vote
                </a>
            <?php else: ?>
                <span class="block text-center border border-gray-300 text-gray-400 px-3 py-1 rounded-md text-xs cursor-not-allowed">
                    No Vote Yet
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- LOGOUT BUTTON -->
    <div class="mt-4 mx-4 mb-4">
        <a href="../logout.php"
           class="block text-center border border-gray-700 text-black px-3 py-1 rounded-md text-xs hover:bg-gray-200 transition">
            Logout
        </a>
    </div>
</aside>


<script>
  // Overlay toggle
  const profileBtn = document.getElementById('profile-btn');
  const overlay = document.getElementById('profile-overlay');
  const dim = document.getElementById('dim-bg');
  const closeBtn = document.getElementById('close-profile');
  profileBtn.onclick = () => { overlay.classList.add('active'); dim.classList.add('active'); document.body.style.overflow='hidden'; };
  closeBtn.onclick = () => { overlay.classList.remove('active'); dim.classList.remove('active'); document.body.style.overflow=''; };
  dim.onclick = () => closeBtn.click();

  // Edit modal
  const editModal = document.getElementById('edit-modal');
  document.getElementById('cancel-edit').onclick = () => editModal.classList.add('hidden');

  // Avatar upload overlay
  const avatarFile = document.getElementById('avatar-overlay-file');
  const overlayAvatar = document.getElementById('overlay-avatar');
  avatarFile.onchange = e => {
    const f = e.target.files[0]; if(!f) return;
    const fd = new FormData(); fd.append('avatar', f); fd.append('action','upload_avatar');
    fetch('',{method:'POST',body:fd}).then(()=>location.reload());
  };
</script>

<?php
if(!empty($messages)){
    echo "<script>";
    foreach($messages as $m) echo "alert('{$m['text']}');";
    echo "</script>";
}
?>
</body>
</html>

<?php 
session_start();
include '../database.php';
$uploadDir = '../uploads/announcements/';

// ← PLACE THE AJAX HANDLING & POST PROCESSING CODE HERE
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $announcementId = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $postedBy = $_SESSION['admin_name'] ?? 'Admin';
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;

    // IMAGE HANDLING
    $image = $_POST['existing_image'] ?? '';
    if (!empty($_FILES['image']['name'])) {
        $fileName = time().'_'.basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
        $image = 'uploads/announcements/' . $fileName;
    }



    // IMAGE HANDLING
    $image = $_POST['existing_image'] ?? '';
    if (!empty($_FILES['image']['name'])) {
        $fileName = time().'_'.basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
        $image = 'uploads/announcements/' . $fileName;
    }

    // ADD ANNOUNCEMENT
    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO announcements (title, content, category, posted_by, image, start_date, end_date, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssssss", $title, $content, $category, $postedBy, $image, $startDate, $endDate);
        $stmt->execute();
    }

    // EDIT ANNOUNCEMENT
    if ($action === 'edit' && $announcementId) {
        $stmt = $conn->prepare("UPDATE announcements 
            SET title=?, content=?, category=?, image=?, start_date=?, end_date=? 
            WHERE id=?");
        $stmt->bind_param("ssssssi", $title, $content, $category, $image, $startDate, $endDate, $announcementId);
        $stmt->execute();
    }

    // DELETE ANNOUNCEMENT
    if ($action === 'delete' && $announcementId) {
        $res = $conn->query("SELECT image FROM announcements WHERE id=$announcementId");
        if ($row = $res->fetch_assoc()) {
            if ($row['image'] && file_exists('../'.$row['image'])) {
                unlink('../'.$row['image']);
            }
        }
        $stmt = $conn->prepare("DELETE FROM announcements WHERE id=?");
        $stmt->bind_param("i", $announcementId);
        $stmt->execute();
    }

    header("Location: admin_announcements.php?success=1");
    exit;
}

// FETCH ANNOUNCEMENTS
$announcements = [];
$result = $conn->query("SELECT * FROM announcements ORDER BY id DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) $announcements[] = $row;
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Announcements | OMSC VoteSphere</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

<?php if(isset($_GET['success'])): ?>
<div id="notif" class="fixed top-5 left-1/2 -translate-x-1/2 bg-green-600 text-white px-6 py-2 rounded shadow">
    Announcement saved successfully!
</div>
<script>
setTimeout(()=>{ document.getElementById('notif').remove(); }, 2000);
</script>
<?php endif; ?>

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

<!-- SIDEBAR -->
<aside id="sidebar" class="w-64 bg-white shadow-md h-screen fixed top-20 left-0 hidden md:block transition-all">
  <div class="p-6 border-b">
    <h2 class="text-[#1e3a8a] font-semibold">Admin Menu</h2>
  </div>
  <ul class="mt-4 text-gray-700">
    <li><a href="../admin/admin_dashboard.html" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-tachometer-alt text-blue-600 w-5"></i><span class="ml-3">Dashboard</span></a></li>
    <li><a href="../admin/manage_candidates.php" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-user-tie text-blue-600 w-5"></i><span class="ml-3">Manage Candidates</span></a></li>
    <li><a href="../admin/manage_voters.html" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-users text-blue-600 w-5"></i><span class="ml-3">Manage Voters</span></a></li>
    <li><a href="../admin/ongoing_elections.html" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-calendar-check text-blue-600 w-5"></i><span class="ml-3">Elections</span></a></li>
    <li><a href="../admin/results.html" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-chart-bar text-blue-600 w-5"></i><span class="ml-3">Results</span></a></li>
    <li><a href="../admin/admin_announcements.php" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition bg-blue-100 border-blue-500"><i class="fas fa-bullhorn text-blue-600 w-5"></i><span class="ml-3">Announcements</span></a></li>
    <li><a href="../admin/settings.html" class="sidebar-link flex items-center px-6 py-3 hover:bg-blue-50 border-l-4 border-transparent hover:border-blue-500 transition"><i class="fas fa-cogs text-blue-600 w-5"></i><span class="ml-3">Settings</span></a></li>
    <li><a href="../logout.php" class="flex items-center px-6 py-3 hover:bg-red-50 border-l-4 border-transparent hover:border-red-500 transition"><i class="fas fa-sign-out-alt text-red-500 w-5"></i><span class="ml-3">Logout</span></a></li>
  </ul>
</aside>

<script>
  const currentPage = window.location.pathname.split("/").pop();
  document.querySelectorAll(".sidebar-link").forEach(link => {
    if (link.getAttribute("href").includes(currentPage)) {
      link.classList.add("bg-blue-100", "border-blue-500");
      link.classList.remove("border-transparent");
    }
  });
</script>

<main class="flex-1 md:ml-64 p-6">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-[#0a2342]">Announcements</h2>
    <button onclick="openModal('add')" class="bg-gradient-to-r from-blue-600 to-indigo-500 text-white px-5 py-2 rounded-lg shadow hover:from-indigo-600 hover:to-blue-500 transition"><i class="fas fa-plus mr-2"></i>Post Announcement</button>
  </div>

  <!-- Filter -->
  <div class="flex gap-3 my-4">
    <input id="searchInput" type="text" placeholder="Search announcements..." class="px-3 py-2 border rounded w-64 text-sm">
    <button class="category-btn bg-blue-600 text-white px-4 py-2 rounded" data-cat="all">All</button>
    <button class="category-btn bg-blue-200 px-4 py-2 rounded" data-cat="Voting Info">Voting Info</button>
    <button class="category-btn bg-blue-200 px-4 py-2 rounded" data-cat="Event Reminder">Event Reminder</button>
    <button class="category-btn bg-blue-200 px-4 py-2 rounded" data-cat="System Notice">System Notice</button>
    <button class="category-btn bg-blue-200 px-4 py-2 rounded" data-cat="Results">Results</button>
  </div>

  <!-- Announcements Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="announcementsGrid">
    <?php foreach ($announcements as $ann): ?>
    <div class="announcement-card bg-white rounded-2xl shadow-lg p-5 border border-gray-200 hover:shadow-xl transition relative"
         data-category="<?= htmlspecialchars($ann['category']) ?>"
         data-start-date="<?= htmlspecialchars($ann['start_date']) ?>"
         data-end-date="<?= htmlspecialchars($ann['end_date']) ?>">
      <?php if($ann['image']): ?>
      <img src="../<?= htmlspecialchars($ann['image']) ?>" alt="announcement image" class="w-full h-auto rounded">
      <?php endif; ?>
      <div class="flex justify-between items-start">
        <div>
          <h4 class="font-semibold text-xl text-[#0a2342]"><?= htmlspecialchars($ann['title']) ?></h4>
          <p class="text-sm text-gray-500 mt-1">Posted by <?= htmlspecialchars($ann['posted_by']) ?> — <?= date("M d, Y", strtotime($ann['created_at'])) ?></p>
          <span class="inline-block mt-2 px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-medium"><?= htmlspecialchars($ann['category']) ?></span>
        </div>
        <div class="flex gap-2">
          <button onclick="openModal('edit', <?= $ann['id'] ?>)" class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm">Edit</button>
          <form method="POST" class="inline">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $ann['id'] ?>">
            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">Delete</button>
          </form>
        </div>
      </div>
      <p class="mt-4 text-gray-700"><?= $ann['content'] ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</main>
</div>

<!-- Modal -->
<div id="announcementModal" class="fixed inset-0 bg-black/50 flex items-start justify-center opacity-0 pointer-events-none transition-all z-50">
  <div class="bg-white rounded-xl shadow-lg w-full max-w-lg mt-24 p-6 relative max-h-[80vh] overflow-y-auto">
    <h3 id="modalTitle" class="text-2xl font-semibold mb-4">Post Announcement</h3>
    <form id="announcementForm" method="POST" enctype="multipart/form-data" onsubmit="return submitForm();">
      <input type="hidden" name="action" id="formAction">
      <input type="hidden" name="id" id="announcementId">
      <input type="hidden" name="existing_image" id="existing_image">
      <input type="hidden" name="category" id="category">

      <div class="mb-3">
        <label class="block text-sm font-medium mb-1">Title</label>
        <input type="text" name="title" id="title" class="w-full border-2 border-blue-500 rounded px-3 py-2 min-h-[50px] focus:outline-none focus:ring-2 focus:ring-blue-300" required>
      </div>

      <div class="mb-3">
        <label class="block text-sm font-medium mb-1">Content</label>
        <div id="contentEditor" contenteditable="true"
             class="w-full border-2 border-blue-500 rounded px-3 py-2 min-h-[100px] focus:outline-none focus:ring-2 focus:ring-blue-300"></div>
        <div class="mb-2 flex gap-2 border-b pb-1">
          <button type="button" onclick="formatText('bold', this)" class="px-2 py-1 rounded hover:bg-gray-200 font-bold">B</button>
          <button type="button" onclick="formatText('italic', this)" class="px-2 py-1 rounded hover:bg-gray-200 italic">I</button>
          <button type="button" onclick="formatText('underline', this)" class="px-2 py-1 rounded hover:bg-gray-200 underline">U</button>
          <button type="button" onclick="formatText('insertUnorderedList', this)" class="px-2 py-1 rounded hover:bg-gray-200">&bull; List</button>
          <button type="button" onclick="formatText('insertOrderedList', this)" class="px-2 py-1 rounded hover:bg-gray-200">1. List</button>
        </div>
        <textarea name="content" id="content" class="hidden" required></textarea>
      </div>

      <div class="mb-3">
        <label class="block text-sm font-medium mb-1">Upload Image (optional)</label>
        <input type="file" name="image" id="image" class="w-full border rounded px-3 py-2" accept="image/*">
      </div>

      <div class="flex justify-end gap-4 mt-4">
        <button type="button" onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
// Text formatting in editor
function formatText(command, button) {
  document.execCommand(command, false, null);
  const toolbarButtons = button.parentElement.querySelectorAll('button');
  toolbarButtons.forEach(btn => btn.classList.remove('bg-gray-300'));
  if (document.queryCommandState(command)) button.classList.add('bg-gray-300');
}

// Open Modal
function openModal(mode, id = null) {
  const modal = document.getElementById('announcementModal');
  modal.classList.remove('opacity-0','pointer-events-none');

  const editor = document.getElementById('contentEditor');
  const textarea = document.getElementById('content');
  const form = document.getElementById('announcementForm');

  form.reset();
  editor.innerHTML = '';
  textarea.value = '';

  if (mode === 'add') {
    document.getElementById('modalTitle').innerText = 'Post Announcement';
    document.getElementById('formAction').value = 'add';
    document.getElementById('existing_image').value = '';
    document.getElementById('announcementId').value = '';
  }

  if (mode === 'edit') {
    document.getElementById('modalTitle').innerText = 'Edit Announcement';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('announcementId').value = id;

    const card = document.querySelector(`.announcement-card button[onclick*="${id}"]`).closest('.announcement-card');
    document.getElementById('title').value = card.querySelector('h4').innerText;

    const content = card.querySelector('p.mt-4')?.innerHTML || '';
    editor.innerHTML = content;
    textarea.value = content;

    document.getElementById('existing_image').value = card.querySelector('img') ? card.querySelector('img').src.replace('../','') : '';
    document.getElementById('start_date').value = card.dataset.startDate || '';
    document.getElementById('end_date').value = card.dataset.endDate || '';
    document.getElementById('category').value = card.dataset.category || 'Voting Info';
  }
}


// Submit form via AJAX
function submitForm() {
  const form = document.getElementById('announcementForm');
  const editor = document.getElementById('contentEditor');
  const textarea = document.getElementById('content');
  textarea.value = editor.innerHTML;

  if (!textarea.value.trim()) {
    alert('Content cannot be empty.');
    return false;
  }

  // Set category based on active filter button
  const activeCategoryBtn = document.querySelector(".category-btn.bg-blue-600");
  const category = activeCategoryBtn ? activeCategoryBtn.dataset.cat : 'Voting Info';
  document.getElementById('category').value = category;

  const formData = new FormData(form);
  fetch('admin_announcements.php', {
    method: 'POST',
    body: formData,
    headers: {'X-Requested-With': 'XMLHttpRequest'}
  })
  .then(res => res.text())
  .then(html => {
    document.getElementById('announcementsGrid').innerHTML = html;
    closeModal();
    filterAnnouncements(); // reapply filter/search
  })
  .catch(err => alert('Error saving announcement'));
  return false;
}

// Delete via AJAX
document.addEventListener('click', function(e){
  if(e.target.closest('.delete-form button')){
    e.preventDefault();
    const form = e.target.closest('form');
    const formData = new FormData(form);
    fetch('admin_announcements.php', {
      method: 'POST',
      body: formData,
      headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(res => res.text())
    .then(html => {
      document.getElementById('announcementsGrid').innerHTML = html;
      filterAnnouncements(); // reapply filter/search
    });
  }
});


// Close Modal
function closeModal() {
  const modal = document.getElementById('announcementModal');
  modal.classList.add('opacity-0','pointer-events-none');
}

// Click outside modal to close
document.getElementById('announcementModal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

// Filtering
const buttons = document.querySelectorAll(".category-btn");
const cards = document.querySelectorAll(".announcement-card");
const searchInput = document.getElementById("searchInput");
let activeCategory = "all";

function filterAnnouncements() {
  const keyword = searchInput.value.toLowerCase();
  cards.forEach(card => {
    const cardCat = card.dataset.category;
    const text = card.innerText.toLowerCase();
    const matchCategory = (activeCategory === "all" || cardCat === activeCategory);
    const matchSearch = text.includes(keyword);
    if (matchCategory && matchSearch) {
      card.classList.remove("hidden", "opacity-0");
      card.classList.add("opacity-100");
    } else {
      card.classList.add("opacity-0");
      setTimeout(() => card.classList.add("hidden"), 200);
    }
  });
}

buttons.forEach(btn => {
  btn.addEventListener("click", () => {
    activeCategory = btn.dataset.cat;
    buttons.forEach(b => b.classList.remove("bg-blue-600","text-white"));
    btn.classList.add("bg-blue-600","text-white");
    filterAnnouncements();
  });
});

searchInput.addEventListener("keyup", filterAnnouncements);
</script>

</body>
</html>

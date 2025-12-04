<?php
session_start();
include '../database.php';

if (!$conn) die("Database connection failed.");

// Predefined positions and grouping
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


// Fetch election types for dropdown
$election_types = [];
$res_types = $conn->query("SELECT DISTINCT election_type FROM candidates");
while($row = $res_types->fetch_assoc()) $election_types[] = $row['election_type'];

// Selected election type
$selectedType = $_GET['etype'] ?? ($election_types[0] ?? '');
if(!isset($positions[$selectedType])) die("Invalid election type.");

// Fetch results
$all_election_results = [];
$stmt = $conn->prepare("SELECT position, name, COALESCE(partylist,'Independent') AS party, votes 
                        FROM candidates 
                        WHERE election_type = ? 
                        ORDER BY position, votes DESC");
$stmt->bind_param("s",$selectedType);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $all_election_results[$row['position']][] = [
        'name'=>$row['name'],
        'party'=>$row['party'],
        'votes'=>(int)$row['votes']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Partial Results | Student</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans min-h-screen">

<!-- Header -->
<div class="bg-blue-900 text-white p-5 shadow">
    <h1 class="text-xl font-bold"><?= htmlspecialchars($selectedType) ?> Election - Partial Results</h1>
    <p class="text-sm text-blue-200 mt-1">Updated live every 60 seconds</p>
</div>

<!-- Election Type Dropdown -->
<div class="max-w-6xl mx-auto px-4 py-4">
    <form method="GET" class="w-full md:w-1/3">
        <label class="text-gray-700 font-medium">Select Election Type:</label>
        <select name="etype" onchange="this.form.submit()" class="border p-2 rounded w-full mt-1">
            <?php foreach($election_types as $et): ?>
                <option value="<?= htmlspecialchars($et) ?>" <?= $selectedType==$et?'selected':'' ?>>
                    <?= htmlspecialchars($et) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<!-- Partial Results -->
<div class="max-w-6xl mx-auto px-4 py-6 space-y-4">

<?php
foreach($positions[$selectedType] as $key => $posGroup) {

    // Flatten grouped positions
    if(is_array($posGroup)) {
        $groupName = $key;
        $cands = [];
        foreach($posGroup as $pos) {
            if(!empty($all_election_results[$pos])) {
                $cands = array_merge($cands, $all_election_results[$pos]);
            }
        }
    } else {
        $groupName = $posGroup;
        $cands = $all_election_results[$posGroup] ?? [];
    }

    if(empty($cands)) continue;

    usort($cands, fn($a,$b)=>$b['votes']-$a['votes']);
    $maxVotes = max(array_column($cands,'votes'));
?>

<div class="bg-white rounded-lg shadow p-3">
    <h2 class="font-semibold text-gray-800 mb-2"><?= $groupName ?></h2>

    <div class="space-y-1">
        <?php foreach($cands as $c): 
            $widthPercent = $maxVotes>0 ? ($c['votes']/$maxVotes)*100 : 0;
            $colorClass = sprintf("#%06X", mt_rand(0, 0xFFFFFF)); // random color for each candidate
        ?>
        <div class="flex items-center justify-between text-xs font-medium">
            <span class="text-gray-700"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['party']) ?>)</span>
            <span class="text-gray-600"><?= $c['votes'] ?> votes</span>
        </div>
        <div class="w-full bg-gray-200 h-2 rounded-full">
            <div class="h-2 rounded-full transition-all duration-500" style="width: <?= $widthPercent ?>%; background-color: <?= $colorClass ?>"></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php } ?>

</div>

<script>
// Auto-refresh every 60s
setInterval(()=>location.reload(),60000);
</script>

</body>
</html>

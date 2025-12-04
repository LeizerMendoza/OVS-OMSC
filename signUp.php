<?php
session_start();
include 'database.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['student_id']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $contact_no = trim($_POST['contact_no']);
    $section = trim($_POST['section']);
    $course = $_POST['course'];
 // Collect club selection (array) and convert to comma-separated string
$club_array = $_POST['club'] ?? [];
if (count($club_array) > 3) {
    $error_message = "You can select up to 3 clubs only!";
} else {
    $club = implode(", ", $club_array); // Convert array to string for DB
}
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (
        !empty($student_id) && 
        !empty($fullname) && 
        !empty($email) && 
        !empty($contact_no) && 
        !empty($section) && 
        !empty($course) && 
        !empty($club) && 
        !empty($password) && 
        !empty($confirm_password)
    ) {

        if ($password === $confirm_password) {
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO users 
            (student_id, fullname, email, contact_no, section, course, club, password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssssss", 
                $student_id, 
                $fullname,
                $email,  
                $contact_no, 
                $section,              
                $course, 
                $club, 
                $hashed_password
            );

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = "Registration successful!";
                header("Location: signin.php");
                exit();
            } else {
                $error_message = "Error: Could not register user.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Passwords do not match!";
        }
    } else {
        $error_message = "All fields are required!";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | OMSC Elections</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative flex items-center justify-center h-screen bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 font-sans">

    <div class="bg-white bg-opacity-95 p-2.5 rounded-lg shadow-xl text-center w-full max-w-xs">
        <div class="flex justify-center mb-2">
            <img src="omsc-logo.png" alt="OMSC Logo" class="w-14 h-14">
        </div>

        <h1 class="text-xl font-bold text-[#0D1B2A]">Sign Up</h1>
        <p class="text-gray-700 text-xs mt-1">Create your account</p>

        <?php if (isset($error_message)) { ?>
            <p class="mt-2 text-red-600 text-xs font-medium"><?php echo $error_message; ?></p>
        <?php } ?>

        <form method="POST" class="mt-3 space-y-1.5">
<!-- Student ID -->
<input type="text" name="student_id" placeholder="Student ID" required
    class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">

<!-- Full Name -->
<input type="text" name="fullname" placeholder="Full Name" required
    class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">

<!-- Email -->
<input type="email" name="email" placeholder="Email" required
    class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">

<!-- Contact Number -->
<input type="text" name="contact_no" placeholder="Contact Number" required
    class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">

<!-- Section -->
<input type="text" name="section" placeholder="Section (e.g., BSIT 2A)" required
    class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">

<!-- Course -->
<select name="course" required
    class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
    <option value="">Select Course</option>
    <option value="BSIT">BSIT</option>
    <option value="BEED">BEED</option>
    <option value="CBAM">CBAM</option>
</select>

<!-- CLUB: Professional Compact Multi-Select Dropdown -->
<div class="w-full max-w-xs relative" x-data="{ open: false, selected: [] }">
  <label class="block text-gray-700 text-sm font-medium mb-1">Select 1-3 Clubs</label>

  <!-- Dropdown Button -->
  <button type="button" @click="open = !open"
          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm flex justify-between items-center bg-white shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
    <span x-text="selected.length > 0 ? selected.join(', ') : 'Select Clubs'"></span>
    <svg class="w-4 h-4 text-gray-400 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
    </svg>
  </button>

  <!-- Dropdown Menu -->
  <div x-show="open" x-transition @click.away="open = false"
       class="absolute mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg z-50 max-h-28 overflow-auto">
   <template x-for="club in ['SPORTS CLUB','ENGLISH CLUB','SCI-MATH CLUB','CYMA','UMSO','SAMFILKO',]" :key="club">
  <label class="flex items-center px-3 py-2 cursor-pointer hover:bg-blue-50 text-sm">
    <input type="checkbox" :value="club" x-model="selected" name="club[]" class="mr-2"
           :disabled="selected.length >= 3 && !selected.includes(club)">
    <span x-text="club"></span>
  </label>
</template>


  </div>
</div>
<!-- Alpine.js for interactivity -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Password -->
<input type="password" name="password" placeholder="Password" required
    class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">

<!-- Confirm Password -->
<input type="password" name="confirm_password" placeholder="Confirm Password" required
    class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">

<!-- Submit Button -->
<button type="submit"
    class="w-full bg-blue-500 text-white py-1.5 rounded-md text-xs hover:bg-blue-600 transition duration-300">
    Sign Up
</button>

        </form>

        <!-- OR Separator -->
        <div class="flex items-center my-2">
            <hr class="flex-grow border-gray-300">
            <span class="mx-1.5 text-gray-500 text-[10px] font-medium">OR</span>
            <hr class="flex-grow border-gray-300">
        </div>

        <!-- Continue with Google -->
        <a href="google_login.php"
           class="w-full inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 rounded-md text-xs text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-3.5 h-3.5 mr-2" viewBox="0 0 533.5 544.3">
          <path fill="#4285F4" d="M533.5 278.4c0-17.9-1.6-35-4.7-51.6H272v97.8h146.9c-6.3 34-25 62.9-53.2 82l86 66.9c50.3-46.3 79.8-114.8 79.8-195.1z"/>
          <path fill="#34A853" d="M272 544.3c72.6 0 133.6-24 178.1-65.1l-86-66.9c-24 16.1-54.8 25.5-92.1 25.5-70.8 0-130.8-47.9-152.2-112.3l-88.4 68.5c43.6 86.5 132.7 150.3 240.6 150.3z"/>
          <path fill="#FBBC05" d="M119.7 325.5c-10.2-30.5-10.2-63.2 0-93.7l-88.4-68.5c-38.8 76.6-38.8 168.6 0 245.2l88.4-68.5z"/>
          <path fill="#EA4335" d="M272 107.2c38.9 0 73.7 13.4 101.2 39.6l75.9-75.9C405.6 25.7 344.6 0 272 0 163.1 0 74 63.8 30.4 150.3l88.4 68.5C141.2 155.1 201.2 107.2 272 107.2z"/>
        </svg>
    
            Continue with Google
        </a>

        <div class="mt-3">
            <p class="text-gray-600 text-xs">Already have an account?
                <a href="signin.php" class="text-[#E09F3E] hover:underline">Log In here</a>
            </p>
        </div>
    </div>

</body>
</html>

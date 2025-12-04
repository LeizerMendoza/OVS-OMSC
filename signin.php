<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_id = trim($_POST['login_id']); // student_id OR email
    $password = trim($_POST['password']);

    if (!empty($login_id) && !empty($password)) {

        $query = "SELECT * FROM users WHERE student_id = ? OR email = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $login_id, $login_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

          if (password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['course'] = $user['course'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['contact_no'] = $user['contact_no'];
    $_SESSION['section'] = $user['section'];
    $_SESSION['club'] = $user['club']; // <-- store all clubs (comma-separated)
    
    header("Location: student/welcome_home.php");
    exit();

            } else {
                $error_message = "Incorrect password!";
            }

        } else {
            $error_message = "Account not found!";
        }

        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Both fields are required!";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | OMSC Elections</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 font-sans">

    <div class="bg-white bg-opacity-95 p-5 rounded-lg shadow-xl text-center w-full max-w-sm">
        
        <div class="flex justify-center mb-3">
            <img src="omsc-logo.png" alt="OMSC Logo" class="w-16 h-16">
        </div>

        <h1 class="text-2xl font-bold text-[#0D1B2A]">Sign In</h1>
        <p class="text-gray-700 text-sm mt-1">Welcome back to OMSC Elections</p>

        <?php if (isset($_SESSION['success_message'])) { ?>
            <p class="mt-2 text-green-600 text-sm font-medium">
                <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                ?>
            </p>
        <?php } ?>

        <?php if (isset($error_message)) { ?>
            <p class="mt-2 text-red-600 text-sm font-medium"><?php echo $error_message; ?></p>
        <?php } ?>

        <form method="POST" class="mt-4 space-y-3">

            <input type="text" name="login_id" placeholder="Student ID or Email" required
               class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">

            <input type="password" name="password" placeholder="Password" required
         class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">


            <button type="submit"
                class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300">
                Sign In
            </button>

        </form>

        <!-- OR Separator -->
        <div class="flex items-center my-3">
            <hr class="flex-grow border-gray-300">
            <span class="mx-2 text-gray-500 text-sm font-medium">OR</span>
            <hr class="flex-grow border-gray-300">
        </div>

        <!-- Google Login -->
        <a href="google_login.php"
           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 transition">
            <svg class="w-3.5 h-3.5 mr-2" viewBox="0 0 533.5 544.3">
          <path fill="#4285F4" d="M533.5 278.4c0-17.9-1.6-35-4.7-51.6H272v97.8h146.9c-6.3 34-25 62.9-53.2 82l86 66.9c50.3-46.3 79.8-114.8 79.8-195.1z"/>
          <path fill="#34A853" d="M272 544.3c72.6 0 133.6-24 178.1-65.1l-86-66.9c-24 16.1-54.8 25.5-92.1 25.5-70.8 0-130.8-47.9-152.2-112.3l-88.4 68.5c43.6 86.5 132.7 150.3 240.6 150.3z"/>
          <path fill="#FBBC05" d="M119.7 325.5c-10.2-30.5-10.2-63.2 0-93.7l-88.4-68.5c-38.8 76.6-38.8 168.6 0 245.2l88.4-68.5z"/>
          <path fill="#EA4335" d="M272 107.2c38.9 0 73.7 13.4 101.2 39.6l75.9-75.9C405.6 25.7 344.6 0 272 0 163.1 0 74 63.8 30.4 150.3l88.4 68.5C141.2 155.1 201.2 107.2 272 107.2z"/>
        </svg>
    
            Continue with Google
        </a>

        <div class="mt-4">
            <p class="text-gray-600 text-sm">Don't have an account?
                <a href="signUp.php" class="text-[#E09F3E] hover:underline">Register here</a>
            </p>
        </div>

    </div>

</body>
</html>

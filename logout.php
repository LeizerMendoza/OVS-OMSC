<?php
session_start();
session_unset(); 
session_destroy(); 

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out...</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="3;url=home.html"> 
</head>
<body class="relative flex items-center justify-center min-h-screen bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 font-sans">

    <?php if (isset($notification_message)): ?>
        <div id="notification-bar" class="fixed top-0 left-0 w-full p-4 text-white text-center 
            <?= $notification_type === 'success' ? 'bg-green-500' : ($notification_type === 'warning' ? 'bg-yellow-500' : 'bg-red-500') ?> 
            transition-all duration-300 transform translate-y-[-100%]">
            <p><?= $notification_message ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white bg-opacity-90 p-6 rounded-lg shadow-lg text-center w-full max-w-md">
        <div class="flex justify-center mb-4">
            <img src="omsc-logo.png" alt="OMSC Logo" class="w-16 h-16">
        </div>
        <h2 class="text-xl font-bold text-[#0D1B2A]">
             You have been logged out.
        </h2>
        <p class="text-gray-600 mt-2">Redirecting to the homepage...</p>

        <div class="mt-4">
            <p class="text-sm text-gray-500">If you're not redirected,</p>
            <a href="home.html" class="text-[#E09F3E] font-bold hover:underline">click here</a>.
        </div>
    </div>

    <script>

        window.onload = function() {
            const notification = document.getElementById('notification-bar');
            if (notification) {
                setTimeout(function() {
                    notification.style.transform = "translateY(0)";
                }, 100); 
                
                setTimeout(function() {
                    notification.style.transform = "translateY(-100%)";
                }, 5000);
            }
        }
    </script>

</body>
</html>

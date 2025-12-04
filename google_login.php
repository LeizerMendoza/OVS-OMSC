<?php
session_start();
require_once 'vendor/autoload.php'; // path to Google Client library
include 'database.php';

$client = new Google_Client();
$client->setClientId('YOUR_CLIENT_ID_HERE');
$client->setClientSecret('YOUR_CLIENT_SECRET_HERE');
$client->setRedirectUri('http://yourdomain.com/google_login.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if(!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();

        $email = $google_account_info->email;
        $name  = $google_account_info->name;

        // Check if user exists in your DB
        $query = "SELECT id, role FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if(mysqli_stmt_num_rows($stmt) > 0){
            mysqli_stmt_bind_result($stmt, $id, $role);
            mysqli_stmt_fetch($stmt);
        } else {
            // New Google user: register as voter by default
            $role = 'voter';
            $insert = "INSERT INTO users (name, email, role) VALUES (?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $insert);
            mysqli_stmt_bind_param($stmt_insert, "sss", $name, $email, $role);
            mysqli_stmt_execute($stmt_insert);
            $id = mysqli_insert_id($conn);
        }

        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;

        if($role === 'admin'){
            header("Location: admin_dashboard.php");
        } else {
            header("Location: welcome_home.php");
        }
        exit();
    } else {
        echo "Google Login Failed: " . $token['error'];
    }
} else {
    $auth_url = $client->createAuthUrl();
    header("Location: $auth_url");
    exit();
}
?>

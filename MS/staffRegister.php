<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'connect.php';
include 'header.php';
include 'mail.php';

$error = "";
$success = "";

$user = $_SESSION['user'];

if (!isset($user['id']) || $user['role'] !== 'ADMIN') {
    header("Location: library/login.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $username = addslashes(trim($_POST['username']));
    $email = addslashes(trim($_POST['email']));
    $role = 'STAFF';

    if (empty($username) || empty($email)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
    
        $checkEmailQuery = "SELECT id FROM users WHERE email = '$email'";
        $checkEmailResult = myQuery($checkEmailQuery, $checkEmailError);

        if ($checkEmailResult) {
            if (mysqli_num_rows($checkEmailResult) > 0) {
                $error = "Email is already registered.";
            } else {
            
                try {
                    $token = bin2hex(random_bytes(16));
                } catch (Exception $e) {
                    $error = "Failed to generate verification token.";
                }

                if (empty($error)) {
                    $verificationUrl = "http://10.1.7.100:7777/st042.site/library/setPassword.php?token=" . urlencode($token);

                
                    $insertQuery = "INSERT INTO users (role, username, email, verification_url) VALUES ('$role', '$username', '$email', '$verificationUrl')";
                    $insertResult = myQuery($insertQuery, $insertError);

                    if ($insertResult) {
                    
                        $emailSubject = "Set Your Password for Library Staff Account";
                        $emailBody = "Hello $username,<br><br>"
                            . "You have been added as a staff member. Please click the link below to set your password:<br>"
                            . "<a href='$verificationUrl'>$verificationUrl</a><br><br>"
                            . "If you did not request this, please contact the admin.";

                        //SEND THE MAIL HERE!
                        if (sendMail($email, $emailSubject, $emailBody)) {
                            $success = "Staff registered successfully. An email has been sent to set their password.";
                        } else {
                        
                            $deleteQuery = "DELETE FROM users WHERE email = '$email'";
                            $deleteResult = myQuery($deleteQuery, $deleteError);

                            if (!$deleteResult) {
                                $error .= " Additionally, failed to delete user: " . htmlspecialchars($deleteError);
                            }

                            $error = "Failed to send verification email. Staff registration aborted.";
                        }
                    } else {
                        $error = "Failed to insert staff information: " . htmlspecialchars($insertError);
                    }
                }
            }
            mysqli_free_result($checkEmailResult);
        } else {
            $error = "Failed to execute email check query: " . htmlspecialchars($checkEmailError);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-100 to-purple-100 min-h-screen ">
    <div class="flex justify-center items-center"> 
    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Register New Staff</h1>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4 border border-red-300">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4 border border-green-300">
                <?= htmlspecialchars($success) ?>   
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="space-y-6">
            <div>
                <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                <input type="text" id="username" name="username" class="w-full p-6 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div>
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" class="w-full p-6 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-4 rounded-lg hover:bg-blue-700 transition duration-300">
                Register Staff
            </button>
        </form>
        
    </div>
    
    </div>
</body>
</html>
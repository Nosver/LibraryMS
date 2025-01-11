<?php

session_start();
require 'connect.php';
include 'header.php';
include 'mail.php';

$error = "";
$success = "";




if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['token'])) {
        $token = addslashes(trim($_GET['token']));

        $checkTokenQuery = "SELECT id FROM users WHERE verification_url LIKE '%$token%'";
        $checkTokenResult = myQuery($checkTokenQuery);

        if ($checkTokenResult && mysqli_num_rows($checkTokenResult) > 0) {
        
        } else {
            $error = "Invalid or expired token.";
        }
    } else {
        $error = "No token provided.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['token'], $_POST['password'], $_POST['confirm_password'])) {
        $token = addslashes(trim($_POST['token']));
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        if (empty($password) || empty($confirm_password)) {
            $error = "All fields are required.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters long.";
        } else {
            $checkTokenQuery = "SELECT id FROM users WHERE verification_url LIKE '%$token%'";
            $checkTokenResult = myQuery($checkTokenQuery);

            if ($checkTokenResult && mysqli_num_rows($checkTokenResult) > 0) {
                $user = mysqli_fetch_assoc($checkTokenResult);
                $user_id = $user['id'];
            
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $updateQuery = "UPDATE users SET password = '$hashed_password', verification_url = NULL WHERE id = '$user_id'";
                $updateResult = myQuery($updateQuery);

                if ($updateResult) {
                    $success = "Your password has been set successfully. You can now <a href='login.php' class='text-blue-500 underline'>log in</a>.";
                } else {
                    $error = "Failed to update password. Please try again later.";
                }
            } else {
                $error = "Invalid or expired token.";
            }
        }
    } else {
        $error = "Invalid form submission.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Your Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirm_password = document.getElementById("confirm_password").value;
            var errorMessage = document.getElementById("error-message");
            
            errorMessage.innerHTML = ""; // Reset error message
            
            if (password.length < 6) {
                errorMessage.innerHTML = "Password must be at least 6 characters long.";
                return false;
            }
            
            if (password !== confirm_password) {
                errorMessage.innerHTML = "Passwords do not match.";
                return false;
            }
            
            return true;
        }
    </script>
</head>
<body class="bg-gradient-to-r from-green-100 to-teal-100 min-h-screen flex items-center justify-center">
    <div class="flex justify-center items-center"> 

    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Set Your Password</h1>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4 border border-red-300">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4 border border-green-300">
                <?= $success ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($success) && empty($error) && isset($_GET['token'])): ?>
            <form method="POST" action="" class="space-y-6" onsubmit="return validatePassword()">
                <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">
                
                <div>
                    <label for="password" class="block text-gray-700 font-medium mb-2">New Password</label>
                    <input type="password" id="password" name="password" value="<?= htmlspecialchars($password ?? '') ?>" class="w-full p-6 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" required>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" value="<?= htmlspecialchars($confirm_password ?? '') ?>" class="w-full p-6 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" required>
                </div>
                
                <div id="error-message" class="text-red-600 mt-2"></div>
                
                <button type="submit" class="w-full bg-teal-600 text-white font-semibold py-4 rounded-lg hover:bg-teal-700 transition duration-300">
                    Set Password
                </button>
            </form>
        <?php endif; ?>
        
        <a href="login.php" class="block text-center text-teal-500 mt-4 hover:underline">Back to Login</a>
    </div>
    </div>

</body>
</html>

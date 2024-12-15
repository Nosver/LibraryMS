<?php
session_start();
require 'connect.php';
include 'header.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = 'CUSTOMER';
    $verificationUrl = uniqid("verify_", true);

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } else {
        try {
        
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        
            $checkQuery = "SELECT COUNT(*) AS count FROM users WHERE email = '" . addslashes($email) . "' OR username = '" . addslashes($username) . "'";
            $result = myQuery($checkQuery);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                if ($row['count'] > 0) {
                    $error = "Username or email already exists!";
                } else {
                
                    $insertQuery = "INSERT INTO users (role, username, email, password, verification_url) VALUES ("
                        . "'" . addslashes($role) . "', "
                        . "'" . addslashes($username) . "', "
                        . "'" . addslashes($email) . "', "
                        . "'" . addslashes($hashedPassword) . "', "
                        . "'" . addslashes($verificationUrl) . "')";
                    $insertStmt = myQuery($insertQuery);

                    if ($insertStmt) {
                        $success = "User registered successfully! Verification URL: $verificationUrl";
                    } else {
                        throw new Exception("Failed to insert user into the database.");
                    }
                }
            } else {
                throw new Exception("Failed to check existing users.");
            }
        } catch (Exception $e) {
        
            $error = "Registration failed: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Register User</title>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl font-bold mb-4 text-gray-700">Register</h2>

        <!-- Error or success message -->
        <?php if ($error): ?>
            <p class="text-red-500 text-sm mb-4"><?= $error ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="text-green-500 text-sm mb-4"><?= $success ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Enter your username"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Enter your email"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Enter your password"
                    required
                />
            </div>

            <div class="flex items-center justify-between">
                <button
                    type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                >
                    Register
                </button>
            </div>
        </form>
    </div>
</body>
</html>
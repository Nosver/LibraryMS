<?php

require 'mail.php';
require 'connect.php';
include 'header.php';


$error = '';
$step = isset($_POST['step']) ? intval($_POST['step']) : 1;
$generated_code = isset($_POST['generated_code']) ? $_POST['generated_code'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 1) {
        $email = $_POST['email'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            $generated_code = rand(100000, 999999);

            $subject = "Password Reset Code";
            $body = "Your password reset code is: <b>$generated_code</b>. It is valid for this session.";
            sendMail($email, $subject, $body);

            $step = 2;
        }
    } elseif ($step == 2) {
        $entered_code = $_POST['sent_code'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($entered_code !== $generated_code) {
            $error = "The reset code is incorrect.";
        } elseif ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            $update_query = "UPDATE users SET password = '" . addslashes($hashed_password) . "' WHERE email = '" . addslashes($email) . "'";
            $result = myQuery($update_query);

            if ($result) {
                echo "<p style='text-align: center; color: green;'>Password has been successfully changed for $email.</p>";
                $step = 1;
                $generated_code = '';
                $email = '';
            } else {
                $error = "Failed to update the password. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex font-poppins items-center justify-center dark:bg-gray-900">
<div class="h-screen w-screen flex justify-center items-center">
    <div class="grid gap-8">
        <div
            id="back-div"
            class="bg-gradient-to-r from-blue-500 to-purple-500 rounded-[26px] m-4"
        >
            <div
                class="border-[20px] border-transparent rounded-[20px] dark:bg-gray-900 bg-white shadow-lg xl:p-10 2xl:p-10 lg:p-10 md:p-10 sm:p-2 m-2"
            >
                <h1 class="pt-8 pb-6 font-bold dark:text-gray-400 text-5xl text-center cursor-default">
                    Forgot Password
                </h1>
                <?php if (!empty($error)) : ?>
                    <p class="text-red-500 text-center font-medium"><?= $error; ?></p>
                <?php endif; ?>

                <form action="" method="post" class="space-y-4">
                    <?php if ($step == 1): ?>
                        <input type="hidden" name="step" value="1">
                        <div>
                            <label for="email" class="mb-2 dark:text-gray-400 text-lg">Email</label>
                            <input
                                id="email"
                                name="email"
                                class="border p-3 shadow-md dark:bg-indigo-700 dark:text-gray-300 dark:border-gray-700 placeholder:text-base focus:scale-105 ease-in-out duration-300 border-gray-300 rounded-lg w-full"
                                type="email"
                                placeholder="Enter your email"
                                required
                            />
                        </div>
                        <button
                            class="bg-gradient-to-r dark:text-gray-300 from-blue-500 to-purple-500 shadow-lg mt-6 p-2 text-white rounded-lg w-full hover:scale-105 hover:from-purple-500 hover:to-blue-500 transition duration-300 ease-in-out"
                            type="submit"
                        >
                            SEND CODE
                        </button>
                    <?php elseif ($step == 2): ?>
                        <input type="hidden" name="step" value="2">
                        <input type="hidden" name="generated_code" value="<?= htmlspecialchars($generated_code); ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($email); ?>">
                        <div>
                            <label for="sent-code" class="mb-2 dark:text-gray-400 text-lg">Sent Code</label>
                            <input
                                id="sent-code"
                                name="sent_code"
                                class="border p-3 shadow-md dark:bg-indigo-700 dark:text-gray-300 dark:border-gray-700 placeholder:text-base focus:scale-105 ease-in-out duration-300 border-gray-300 rounded-lg w-full"
                                type="text"
                                placeholder="Enter the sent code"
                                required
                            />
                        </div>
                        <div>
                            <label for="new-password" class="mb-2 dark:text-gray-400 text-lg">New Password</label>
                            <input
                                id="new-password"
                                name="new_password"
                                class="border p-3 shadow-md dark:bg-indigo-700 dark:text-gray-300 dark:border-gray-700 placeholder:text-base focus:scale-105 ease-in-out duration-300 border-gray-300 rounded-lg w-full"
                                type="password"
                                placeholder="Enter your new password"
                                required
                            />
                        </div>
                        <div>
                            <label for="confirm-password" class="mb-2 dark:text-gray-400 text-lg">Confirm New Password</label>
                            <input
                                id="confirm-password"
                                name="confirm_password"
                                class="border p-3 shadow-md dark:bg-indigo-700 dark:text-gray-300 dark:border-gray-700 placeholder:text-base focus:scale-105 ease-in-out duration-300 border-gray-300 rounded-lg w-full"
                                type="password"
                                placeholder="Confirm your new password"
                                required
                            />
                        </div>
                        <button
                            class="bg-gradient-to-r dark:text-gray-300 from-blue-500 to-purple-500 shadow-lg mt-6 p-2 text-white rounded-lg w-full hover:scale-105 hover:from-purple-500 hover:to-blue-500 transition duration-300 ease-in-out"
                            type="submit"
                        >
                            CHANGE PASSWORD
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>

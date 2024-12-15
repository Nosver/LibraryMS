<?php
session_start();
require 'header.php';


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind CSS Test</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden py-6 sm:py-12 bg-white">
    <div class="max-w-xl px-5 text-center">
        <h2 class="mb-2 text-[42px] font-bold text-zinc-800">Check your inbox</h2>
        <p class="mb-2 text-lg text-zinc-500">We are glad, that you’re with us. We’ve sent you a verification link to the email address <span class="font-medium text-indigo-500"><?php echo $_SESSION['email']; ?></span>.</p>
        <a href="../library/login.php" class="mt-3 inline-block w-96 rounded bg-indigo-600 px-5 py-3 font-medium text-white shadow-md shadow-indigo-500/20 hover:bg-indigo-700">login</a>
    </div>
</div>
</body>

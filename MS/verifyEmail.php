<?php
require 'connect.php';
require 'header.php';
$verificationId = isset($_GET['verify']) ? $_GET['verify'] : null;
$verificationId = "?verify=" . $verificationId;

if ($verificationId) {
    $qry = "SELECT * FROM users WHERE verification_url='$verificationId'";
    $result = myQuery($qry);

    if ($result->num_rows > 0) {
        $updateQry = "UPDATE users SET verification_url = NULL WHERE verification_url = '$verificationId'";
        myQuery($updateQry);
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

<div class="bg-gradient-to-br from-indigo-100 via-purple-200 to-pink-300 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 md:w-1/2 w-full">
        <svg viewBox="0 0 24 24" class="text-green-600 w-16 h-16 mx-auto mb-6 animate-pulse">
            <path fill="currentColor" d="M12,0A12,12,0,1,0,24,12,12.014,12.014,0,0,0,12,0Zm6.927,8.2-6.845,9.289a1.011,1.011,0,0,1-1.43.188L5.764,13.769a1,1,0,1,1,1.25-1.562l4.076,3.261,6.227-8.451A1,1,0,1,1,18.927,8.2Z"></path>
        </svg>
        <div class="text-center">
            <h3 class="text-3xl font-semibold text-gray-800">Verification Successful!</h3>
            <p class="text-gray-600 my-4">Your email has been successfully verified. Thank you for joining us!</p>
            <div class="py-6">
                <a href="../library/login.php" class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-500 transition duration-300 ease-in-out transform hover:scale-105">
                    Login
                </a>
            </div>
        </div>
    </div>
</div>
<?php
    } else {
?>
<div class="bg-gradient-to-br from-indigo-100 via-purple-200 to-pink-300 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 md:w-1/2 w-full">
        <svg viewBox="0 0 24 24" class="text-red-600 w-16 h-16 mx-auto mb-6 animate-pulse">
            <path fill="currentColor" d="M12,0A12,12,0,1,0,24,12,12.014,12.014,0,0,0,12,0Zm1,17H11V15h2ZM11,7h2V13H11Z"></path>
        </svg>
        <div class="text-center">
            <h3 class="text-3xl font-semibold text-gray-800">404 Not Found</h3>
            <p class="text-gray-600 my-4">The verification link is either invalid or has already been used. Please check the link or contact support.</p>
            <div class="py-6">
                <a href="../library/login.php" class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-500 transition duration-300 ease-in-out transform hover:scale-105">
                    Login
                </a>
            </div>
        </div>
    </div>
</div>
<?php
    }
} else {
?>
<div class="bg-gradient-to-br from-indigo-100 via-purple-200 to-pink-300 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 md:w-1/2 w-full">
        <svg viewBox="0 0 45 45" class="w-16 h-16 mx-auto mb-6 animate-pulse">
            <path fill="red" d="M22.675,0.02c-0.006,0-0.014,0.001-0.02,0.001c-0.007,0-0.013-0.001-0.02-0.001C10.135,0.02,0,10.154,0,22.656 c0,12.5,10.135,22.635,22.635,22.635c0.007,0,0.013,0,0.02,0c0.006,0,0.014,0,0.02,0c12.5,0,22.635-10.135,22.635-22.635 C45.311,10.154,35.176,0.02,22.675,0.02z M22.675,38.811c-0.006,0-0.014-0.001-0.02-0.001c-0.007,0-0.013,0.001-0.02,0.001 c-2.046,0-3.705-1.658-3.705-3.705c0-2.045,1.659-3.703,3.705-3.703c0.007,0,0.013,0,0.02,0c0.006,0,0.014,0,0.02,0 c2.045,0,3.706,1.658,3.706,3.703C26.381,37.152,24.723,38.811,22.675,38.811z M27.988,10.578 c-0.242,3.697-1.932,14.692-1.932,14.692c0,1.854-1.519,3.356-3.373,3.356c-0.01,0-0.02,0-0.029,0c-0.009,0-0.02,0-0.029,0 c-1.853,0-3.372-1.504-3.372-3.356c0,0-1.689-10.995-1.931-14.692C17.202,8.727,18.62,5.29,22.626,5.29 c0.01,0,0.02,0.001,0.029,0.001c0.009,0,0.019-0.001,0.029-0.001C26.689,5.29,28.109,8.727,27.988,10.578z"></path> 
        </svg>
        <div class="text-center">
            <h3 class="text-3xl font-semibold text-gray-800">404 Not Found</h3>
            <p class="text-gray-600 my-4">The verification link is invalid or missing.</p>
            <p>Please check the link or contact support.</p>
            <div class="py-6">
                <a href="../library/login.php" class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-500 transition duration-300 ease-in-out transform hover:scale-105">
                    Login
                </a>
            </div>
        </div>
    </div>
</div>
<?php
}
?>

</body>

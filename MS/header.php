<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 flex flex-col min-h-screen">
<header class="fixed top-0 left-0 w-full bg-white text-black p-4 rounded-lg shadow-lg z-50">
    <div class="container mx-auto flex justify-between items-center">
        <div>
            <a href="https://github.com/Nosver">
                <img src="/library/images/nosver.png" alt="Logo" class="h-[60px]">
            </a>
        </div>
        <nav>
            <ul class="flex space-x-6 text-sm font-medium">
                <li>
                    <?php
                    if (isset($_SESSION['user'])) {
                        if ($_SESSION['user']['role'] === 'CUSTOMER') {
                            echo '<a href="bookCatalogue.php" class="hover:underline">Catalogue</a>';
                        } elseif ($_SESSION['user']['role'] === 'STAFF') {
                            echo '<a href="staff.php" class="hover:underline">Dashboard</a>';
                        }
                    } else {
                        echo '<a href="bookCatalogue.php" class="hover:underline">Catalogue</a>';
                    }
                    ?>
                </li>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'CUSTOMER') { ?>
                    <li>
                        <a href="userTransactions.php" class="hover:underline">Transactions</a>
                    </li>
                    <li>
                        <a href="myprofile.php" class="hover:underline">Profile</a>
                    </li>
                    <li>
                        <a href="logout.php" class="hover:underline text-red-500">Logout</a>
                    </li>
                <?php } elseif (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'STAFF') { ?>
                    <li>
                        <a href="staffTransactions.php" class="hover:underline">Transactions</a>
                    </li>

                    <li>
                        <a href="myprofile.php" class="hover:underline">Profile</a>
                    </li>

                    <li>
                        <a href="logout.php" class="hover:underline text-red-500">Logout</a>
                    </li>
                <?php } elseif (!isset($_SESSION['user'])) { ?>
                    <li>
                        <a href="login.php" class="hover:underline text-red-500">Login</a>
                    </li>
                <?php } ?>
            </ul>

        </nav>
    </div>
</header>

<main class=" mt-[90px]">
</main>

</body>
</html>

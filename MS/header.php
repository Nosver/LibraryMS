<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <header class="bg-white-600 text-black p-4 w-full rounded-lg shadow-lg">
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
                        if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'CUSTOMER') { 
                        ?>
                            <a href="bookCatalogue.php" class="hover:underline">Catalogue</a>
                        <?php 
                        } elseif (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'STAFF') { 
                        ?>
                            <a href="staff.php" class="hover:underline">Dashboard</a>
                        <?php 
                        } 
                        ?>
                    </li>

                    <li>
                        <a href="<?php echo isset($_SESSION['user']) && $_SESSION['user']['role'] === 'CUSTOMER' 
                                        ? 'userTransactions.php' 
                                        : 'staffTransactions.php'; ?>" 
                        class="hover:underline">Transactions</a>
                    </li>

                    <li><a href="myprofile.php" class="hover:underline">Profile</a></li>
                   <?php if (isset($_SESSION['user']))
                        echo "<li><a href='logout.php' class='hover:underline text-red-500'>Logout</a></li>";
                        else {
                           echo "<li><a href='login.php' class='hover:underline text-red-500'>Login</a></li>";
                        }?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="mb-[20px]">
    </main>
</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title><?php echo $pageTitle ?? 'Default Title'; ?></title>
</head>
<body class="bg-gray-100 p-6">
    <header class="bg-white-600 text-black p-4 w-full rounded-lg shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <a href="index.php">
                    <img src="/library/images/nosver.png" alt="Logo" class="h-10">
                </a>
            </div>
            <nav>
                <ul class="flex space-x-6 text-sm font-medium">
                    <li><a href="bookCatalogue.php" class="hover:underline">Catalogue</a></li>
                    <li><a href="userTransactions.php" class="hover:underline">Transactions</a></li>
                    <li><a href="myprofile.php" class="hover:underline">Profile</a></li>
                    <li><a href="logout.php" class="hover:underline text-red-500">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="pt-20">
    </main>
</body>
</html>

<?php
session_start();
require 'connect.php';

$error = "";
$user = null;
$transactions = [];
$books = [];

if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

try {

    $queryUser = "SELECT role, username, email FROM users WHERE id = $userId";
    $resultUser = myQuery($queryUser);

    if ($resultUser && $row = mysqli_fetch_assoc($resultUser)) {
        $user = $row;
    } else {
        throw new Exception("Failed to fetch user details.");
    }


    $queryTransactions = "SELECT t.id, b.name AS book_name, t.borrowed_at, t.due_date, t.return_date, t.t_state 
                          FROM transactions t 
                          JOIN books b ON t.book_id = b.id 
                          WHERE t.user_id = $userId";
    $resultTransactions = myQuery($queryTransactions);

    while ($resultTransactions && $row = mysqli_fetch_assoc($resultTransactions)) {
        $transactions[] = $row;
    }


    $queryBooks = "SELECT b.name, b.author, b.location 
                   FROM transactions t 
                   JOIN books b ON t.book_id = b.id 
                   WHERE t.user_id = $userId AND t.return_date IS NULL";
    $resultBooks = myQuery($queryBooks);

    while ($resultBooks && $row = mysqli_fetch_assoc($resultBooks)) {
        $books[] = $row;
    }
} catch (Exception $e) {
    $error = "Error: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>My Profile</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-12 px-6">
        <!-- Profile Section -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-3xl font-bold mb-4 text-gray-700">My Profile</h2>
            <?php if ($error): ?>
                <p class="text-red-500 text-sm mb-4"><?= $error ?></p>
            <?php else: ?>
                <p class="text-lg mb-2"><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
                <p class="text-lg mb-2"><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                <p class="text-lg mb-2"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <?php endif; ?>
        </div>

        <!-- My Transactions Section -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-700">My Transactions</h2>
            <?php if (empty($transactions)): ?>
                <p class="text-gray-500">You have no transactions yet.</p>
            <?php else: ?>
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 border">Transaction ID</th>
                            <th class="px-4 py-2 border">Book Name</th>
                            <th class="px-4 py-2 border">Borrowed At</th>
                            <th class="px-4 py-2 border">Due Date</th>
                            <th class="px-4 py-2 border">Return Date</th>
                            <th class="px-4 py-2 border">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 border"><?= htmlspecialchars($transaction['id']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($transaction['book_name']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($transaction['borrowed_at']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($transaction['due_date']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($transaction['return_date'] ?? 'Not Returned') ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($transaction['t_state']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- My Books Section -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-700">My Books</h2>
            <?php if (empty($books)): ?>
                <p class="text-gray-500">You currently have no borrowed books.</p>
            <?php else: ?>
                <ul class="list-disc pl-5 space-y-2">
                    <?php foreach ($books as $book): ?>
                        <li class="text-lg">
                            <strong><?= htmlspecialchars($book['name']) ?></strong> 
                            by <?= htmlspecialchars($book['author']) ?> 
                            (Location: <?= htmlspecialchars($book['location']) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Logout Button -->
        <div class="text-center">
            <a href="logout.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Logout</a>
        </div>
    </div>
</body>
</html>
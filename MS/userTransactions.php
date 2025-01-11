<?php
session_start();
require 'connect.php';
$pageTitle = 'Transaction History';
require 'header.php';

$error = "";
$transactions = [];

if (!isset($_SESSION['user']['id'])) {
    header("Location: library/login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

// Handle cancellation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'])) {
    $transaction_id = intval($_POST['transaction_id']);

    try {
        // Fetch the book_id for the transaction
        $book_query = "SELECT book_id FROM transactions WHERE id = $transaction_id AND user_id = $userId AND t_state = 'WAITING_APPROVAL'";
        $book_result = myQuery($book_query);

        if ($book_result && $row = mysqli_fetch_assoc($book_result)) {
            $book_id = $row['book_id'];

            // Delete the transaction
            $delete_query = "DELETE FROM transactions WHERE id = $transaction_id AND user_id = $userId";
            if (!myQuery($delete_query)) {
                throw new Exception("Failed to delete the transaction.");
            }

            // Update the book's availability
            $update_book_query = "UPDATE books SET is_available = 1 WHERE id = $book_id";
            if (!myQuery($update_book_query)) {
                throw new Exception("Failed to update book availability.");
            }

            $success = "Reservation canceled successfully!";
        } else {
            throw new Exception("Transaction not found or cannot be canceled.");
        }
    } catch (Exception $e) {
        $error = "Error: " . htmlspecialchars($e->getMessage());
    }
}

// Fetch transactions
try {
    $filter_state = isset($_GET['filter']) ? $_GET['filter'] : null;

    $queryTransactions = "SELECT t.id, b.name AS book_name, t.borrowed_at, t.due_date, t.t_state 
                          FROM transactions t
                          JOIN books b ON t.book_id = b.id
                          WHERE t.user_id = $userId";

    if ($filter_state) {
        $queryTransactions .= " AND t.t_state = '$filter_state'";
    }

    $resultTransactions = myQuery($queryTransactions);

    while ($resultTransactions && $row = mysqli_fetch_assoc($resultTransactions)) {
        $transactions[] = $row;
    }
} catch (Exception $e) {
    $error = "Error fetching transactions: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Transaction History</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-12 px-6">
        <!-- Message Section -->
        <?php if (!empty($error)): ?>
            <p class="text-red-500 text-sm mb-4"><?= $error ?></p>
        <?php elseif (!empty($success)): ?>
            <p class="text-green-500 text-sm mb-4"><?= $success ?></p>
        <?php endif; ?>

        <!-- Filter Section -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <form method="GET" action="" class="mb-6">
                <label for="filter" class="block text-gray-700 font-medium mb-2">Filter by State:</label>
                <select name="filter" id="filter" class="border rounded-lg px-4 py-2 mb-4 w-full">
                    <option value="">All</option>
                    <?php
                    $states = ['WAITING_APPROVAL', 'APPROVED', 'REJECTED', 'DELIVERED', 'RETURNED_GOOD_CONDITION', 'RETURNED_POOR_CONDITION', 'NOT_RETURNED'];
                    foreach ($states as $state) {
                        $selected = ($filter_state === $state) ? 'selected' : '';
                        echo "<option value=\"$state\" $selected>$state</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500">Apply Filter</button>
            </form>
        </div>

        <!-- Transactions Section -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-700">My Transactions</h2>
            <?php if (empty($transactions)): ?>
                <p class="text-gray-500">No transactions found.</p>
            <?php else: ?>
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 border">Book Name</th>
                            <th class="px-4 py-2 border">Borrowed At</th>
                            <th class="px-4 py-2 border">Due Date</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 border"><?= htmlspecialchars($transaction['book_name']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($transaction['borrowed_at']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($transaction['due_date']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($transaction['t_state']) ?></td>
                                <td class="px-4 py-2 border">
                                    <?php if ($transaction['t_state'] === 'WAITING_APPROVAL'): ?>
                                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to cancel the reservation?');">
                                            <input type="hidden" name="transaction_id" value="<?= $transaction['id'] ?>">
                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500">Cancel Reservation</button>
                                        </form>
                                    <?php else: ?>
                                        <button disabled class="px-4 py-2 bg-gray-300 text-gray-600 rounded-md cursor-not-allowed">Already Booked</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

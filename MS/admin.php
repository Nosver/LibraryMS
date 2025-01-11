<?php
session_start();
require 'connect.php';
$pageTitle = 'Admin Analytics';
include 'header.php';

$user = $_SESSION['user'];

if (!isset($user['id']) || $user['role'] !== 'ADMIN') {
    header("Location: library/login.php");
    exit();
}

$staff_query = "SELECT id, username, email FROM users WHERE role = 'STAFF'";
$staff_result = myQuery($staff_query);

$customer_query = "SELECT id, username, email FROM users WHERE role = 'CUSTOMER'";
$customer_result = myQuery($customer_query);

$recent_transactions_query = "
    SELECT 
        t.id AS transaction_id, 
        u.username AS user_name, 
        b.name AS book_name, 
        t.borrowed_at 
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    JOIN books b ON t.book_id = b.id
    ORDER BY t.borrowed_at DESC
    LIMIT 5";

$recent_transactions_result = myQuery($recent_transactions_query);


$fine_transactions_query = "
    SELECT 
        t.id AS transaction_id, 
        u.username AS user_name, 
        t.fine_fee 
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    WHERE t.fine_fee > 0";

$fine_transactions_result = myQuery($fine_transactions_query);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_staff'])) {
    $id = $_POST['id'] ?? '';

    if ($id) {
        $id = (int)$id;
        $queryDeleteStaff = "DELETE FROM users WHERE id = $id AND role = 'STAFF'";
        
        if (myQuery($queryDeleteStaff)) {
            $success = "Staff user deleted successfully.";
            header("Location: library/admin.php");
        } else {
            $error = "Failed to delete staff user.";
        }
    } else {
        $error = "Invalid ID provided.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-3xl font-bold mb-6">Admin Analytics</h1>

    <div class="flex gap-4 mb-6">
        <button id="staffToggle" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500">Show/Hide Staff Members</button>
        <button id="customerToggle" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-500">Show/Hide Customers</button>
        <button id="addingToggle" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-green-500" onclick="window.location.href='staffRegister.php'">Add Staff Member</button>
    </div>

    <div id="staffTable" class="mb-6 hidden">
        <h2 class="text-2xl font-semibold mb-4">All Staff Members</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($staff_result && mysqli_num_rows($staff_result) > 0): ?>
                    <?php foreach ($staff_result as $row): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="admin.php" method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <button type="submit" name="delete_staff" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No staff found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="customerTable" class="mb-6 hidden">
        <h2 class="text-2xl font-semibold mb-4">All Customers</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($customer_result && mysqli_num_rows($customer_result) > 0): ?>
                    <?php foreach ($customer_result as $row): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No customers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mb-6">
        <h2 class="text-2xl font-semibold mb-4">Recent Transactions</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($recent_transactions_result && mysqli_num_rows($recent_transactions_result) > 0): ?>
                    <?php foreach ($recent_transactions_result as $row): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['transaction_id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['book_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['borrowed_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No transactions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <div class="mb-6">
        <h2 class="text-2xl font-semibold mb-4">Fines</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fine Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($fine_transactions_result && mysqli_num_rows($fine_transactions_result) > 0): ?>
                    <?php foreach ($fine_transactions_result as $row): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['transaction_id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['fine_fee']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No fine transactions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <script>
        document.getElementById('staffToggle').addEventListener('click', function() {
            document.getElementById('staffTable').classList.toggle('hidden');
        });

        document.getElementById('customerToggle').addEventListener('click', function() {
            document.getElementById('customerTable').classList.toggle('hidden');
        });
    </script>

</body>
</html>

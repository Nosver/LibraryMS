<?php
session_start();

require 'connect.php';
$pageTitle = 'Transaction History';
include 'header.php';

$GLOBALS['DEBUG_MODE'] = false;
$user = $_SESSION['user'];

if (!isset($user['id']) || $user['role'] !== 'CUSTOMER') {
    header("Location: library/login.php");
    exit();
}

$filter_state = isset($_GET['filter']) ? $_GET['filter'] : null;

$sql = "SELECT t.id, u.username, b.name AS book_name, t.borrowed_at, t.due_date, t.t_state 
        FROM transactions t
        JOIN users u ON t.user_id = u.id
        JOIN books b ON t.book_id = b.id WHERE t.user_id =" . strval($user['id']);

if ($filter_state) {
    $sql .= " AND t.t_state = '$filter_state'";
}

$result = myQuery($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">


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

<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
    <tr>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed At</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">State</th>
    </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <?php foreach ($result as $row): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['book_name']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['borrowed_at']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['due_date']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['t_state']; ?></td>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No transactions found.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>

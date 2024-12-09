<?php
session_start();
require 'connect.php';

$GLOBALS['DEBUG_MODE'] = false;
$user = $_SESSION['user'];

 if (!isset($user['id']) || $user['role'] !== 'STAFF') {
     header("Location: library/login.php");
     exit();
 }

$filter_state = isset($_GET['filter']) ? $_GET['filter'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $transaction_id = intval($_GET['transaction_id']);
    $new_state = ($_POST['action'] === 'approve') ? 'APPROVED' : 'REJECTED';

    $update_query = "UPDATE transactions SET t_state = '$new_state' WHERE id = $transaction_id";
    $result = myQuery($update_query);

    if ($result) {
        echo "<p>Transaction updated successfully!</p>";
    } else {
        echo "<p>Error updating transaction.</p>";
    }
}

$sql = "SELECT t.id, u.username, b.name AS book_name, t.borrowed_at, t.due_date, t.t_state 
        FROM transactions t
        JOIN users u ON t.user_id = u.id
        JOIN books b ON t.book_id = b.id";

if ($filter_state) {
    $sql .= " WHERE t.t_state = '$filter_state'";
}

$result = myQuery($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Transactions</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<h1 class="text-2xl font-bold mb-6">Transactions</h1>

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
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed At</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">State</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
    </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <?php foreach ($result as $row): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['id']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['username']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['book_name']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['borrowed_at']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['due_date']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['t_state']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php if ($row['t_state'] === 'WAITING_APPROVAL'): ?>
                        <form method="POST" class="inline-block">
                            <input type="hidden" name="transaction_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="approve" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-500">Approve</button>
                            <button type="submit" name="action" value="reject" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500">Reject</button>
                        </form>
                    <?php else: ?>
                        <span class="text-gray-500">N/A</span>
                    <?php endif; ?>
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

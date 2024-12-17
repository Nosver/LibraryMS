<?php
session_start();
require 'connect.php';
require 'mail.php';

$pageTitle = 'Staff Dashboard';
include 'header.php';

$GLOBALS['DEBUG_MODE'] = false; 
$user = $_SESSION['user'];


if (!isset($user['id']) || $user['role'] !== 'STAFF') {
    header("Location: library/login.php");
    exit();
}

$filter_state = isset($_GET['filter']) ? $_GET['filter'] : null;

function generateEmailContent($user_email, $book_name) {
    return '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Book Available Notification</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
        <div style="max-width: 600px; margin: 20px auto; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <div style="text-align: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 20px; margin-bottom: 20px;">
                <div style="color: #ef4444; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 8px;">NOSVER</div>
                <h1 style="font-size: 24px; font-weight: 600; color: #333333; margin: 0;">Book Availability Notification</h1>
            </div>
            <div style="color: #4b5563; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                <p>
                    Hello,<br><br>
                    We are pleased to inform you that the book <strong style="color: #111827;">"' . htmlspecialchars($book_name) . '"</strong> is now available.
                    Please visit the library to borrow it at your earliest convenience.
                </p>
                <p>Thank you for choosing our library services!</p>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="http://10.1.7.100:7777/st042.site/library/login.php" 
                   style="display: inline-block; text-align: center; background-color: #2563eb; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: bold; padding: 12px 24px; border-radius: 6px;">
                   Visit Library Site
                </a>
            </div>
        </div>
    </body>
    </html>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $transaction_id = intval($_POST['transaction_id']);
    $book_id = null;
    $book_name = null;

    
    $transaction_query = "SELECT book_id FROM transactions WHERE id = $transaction_id";
    $transaction_result = myQuery($transaction_query);

    if ($transaction_result && $transaction_row = mysqli_fetch_assoc($transaction_result)) {
        $book_id = $transaction_row['book_id'];
        $book_query = "SELECT name FROM books WHERE id = $book_id";
        $book_result = myQuery($book_query);

        if ($book_result && $book_row = mysqli_fetch_assoc($book_result)) {
            $book_name = $book_row['name'];
        }
    } else {
        error_log("Transaction ID $transaction_id couldn'take for book_id.");
    }

    
    $new_state = null;
    switch ($_POST['action']) {
        case 'approve':
            $new_state = 'APPROVED';
            break;
        case 'reject':
            $new_state = 'REJECTED';
            break;
        case 'return_good':
            $new_state = 'RETURNED_GOOD_CONDITION';
            break;
        case 'return_poor':
            $new_state = 'RETURNED_POOR_CONDITION';
            break;
        case 'not_returned':
            $new_state = 'NOT_RETURNED';
            break;
    }

    
    if ($new_state) {
        $update_query = "UPDATE transactions SET t_state = '$new_state' WHERE id = $transaction_id";
        $result = myQuery($update_query);

        if ($result) {
            echo "<p>Transaction updated successfully!</p>";

            
            if (in_array($new_state, ['RETURNED_GOOD_CONDITION', 'RETURNED_POOR_CONDITION'])) {
                $notification_query = "
                    SELECT n.id AS notification_id, u.email AS user_email 
                    FROM notifications n
                    JOIN users u ON n.user_id = u.id
                    WHERE n.book_id = $book_id AND n.sent_at IS NULL
                ";
                $notification_result = myQuery($notification_query);

                if ($notification_result && mysqli_num_rows($notification_result) > 0) {
                    while ($notification_row = mysqli_fetch_assoc($notification_result)) {
                        $notification_id = $notification_row['notification_id'];
                        $user_email = $notification_row['user_email'];

                        
                        $subject = "Book Available Notification";
                        $message = "The book you requested is now available. Please check the library.";

                        if (sendMail($user_email, $subject, generateEmailContent($user_email, $book_name))) {
                            $update_notification_query = "UPDATE notifications SET sent_at = NOW() WHERE id = $notification_id";
                            $update_result = myQuery($update_notification_query);
                            if ($update_result) {
                                echo "<p>Notification sent to $user_email.</p>";
                            } else {
                                error_log("Notification ID $notification_id sent_at could not be updated.");
                            }
                        } else {
                            error_log("Unsuccessful sending mail: $user_email");
                            echo "<p>Failed to send notification to $user_email.</p>";
                        }
                    }
                } else {
                    error_log("No pending notifications for book ID: $book_id");
                    echo "<p>No pending notifications for this book.</p>";
                }
            }
        } else {
            error_log("Transaction ID $transaction_id güncellenemedi.");
            echo "<p>Error updating transaction.</p>";
        }
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
                    <?php elseif ($row['t_state'] === 'APPROVED'): ?>
                        <form method="POST" class="inline-block">
                            <input type="hidden" name="transaction_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="return_good" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-500">Return (Good Condition)</button>
                            <button type="submit" name="action" value="return_poor" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-500">Return (Poor Condition)</button>
                            <button type="submit" name="action" value="not_returned" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500">Not Returned</button>
                        </form>
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

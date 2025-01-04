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
$isStatusUpdateRequired = true;

function sendNotification($new_state, $transaction_id, $book_id, $book_name) {
    if (in_array($new_state, ['RETURNED_GOOD_CONDITION', 'RETURNED_POOR_CONDITION'])) {
        // Update book availability
        $update_query1 = "UPDATE books SET is_available = 1 WHERE id = $book_id";
        if (myQuery($update_query1)) {
            // Check for pending notifications
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
                    $message = generateEmailContent($book_name, 'available');

                    if (sendMail($user_email, $subject, $message)) {
                        $update_notification_query = "UPDATE notifications SET sent_at = NOW() WHERE id = $notification_id";
                        if (myQuery($update_notification_query)) {
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
    } elseif (in_array($new_state, ['APPROVED', 'REJECTED'])) {
        // Fetch user email and book name
        $email_query = "
            SELECT u.email AS user_email, b.name AS book_name
            FROM transactions t
            JOIN users u ON t.user_id = u.id
            JOIN books b ON t.book_id = b.id
            WHERE t.id = $transaction_id
        ";
        $email_result = myQuery($email_query);

        if ($email_result && $email_row = mysqli_fetch_assoc($email_result)) {
            $user_email = $email_row['user_email'];
            $book_name = $email_row['book_name'];

            $subject = $new_state === 'APPROVED' ? "Book Request Approved" : "Book Request Rejected";
            $email_content = generateEmailContent($book_name, strtolower($new_state));

            if (sendMail($user_email, $subject, $email_content)) {
                echo "<p>Email notification sent to $user_email.</p>";
            } else {
                echo "<p>Failed to send email to $user_email.</p>";
                error_log("Email sending failed for $user_email (Transaction ID: $transaction_id).");
            }
        } else {
            echo "<p>Failed to fetch user email or book name for Transaction ID: $transaction_id.</p>";
            error_log("Email or book name not found for Transaction ID: $transaction_id.");
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_fee'])) {
    $confirm_fee = $_POST['confirm_fee'];
    $transaction_id = intval($_POST['transaction_id']);
    $book_id=intval($_POST['book_id']);
    $book_name=$_POST['book_name'];

    $new_state = $_POST['new_state'];

    if ($confirm_fee === 'yes') {
        $updateFeeQry = "UPDATE transactions t set fine_fee=150 WHERE t.id=$transaction_id";
        myQuery($updateFeeQry);

        $update_query = "UPDATE transactions SET t_state = '$new_state' WHERE id = $transaction_id";

        $result = myQuery($update_query);
        sendNotification($new_state,$transaction_id,$book_id,$book_name);
    } else {
        echo "<div class='bg-yellow-100 text-yellow-700 p-4 rounded mt-4'>Transaction update canceled.</div>";
    }
}

function generateEmailContent($book_name, $type)
{
    $titles = [
        'available' => 'Book Availability Notification',
        'approved' => 'Book Request Approved',
        'rejected' => 'Book Request Rejected',
    ];

    $messages = [
        'available' => "We are pleased to inform you that the book <strong>'" . htmlspecialchars($book_name) . "'</strong> is now available. Please visit the library to borrow it at your earliest convenience.",
        'approved' => "We are pleased to inform you that your request for the book <strong>'" . htmlspecialchars($book_name) . "'</strong> has been approved. Please visit the library to borrow the book at your earliest convenience.",
        'rejected' => "We regret to inform you that your request for the book <strong>'" . htmlspecialchars($book_name) . "'</strong> has been rejected. Please contact the library staff for more details.",
    ];

    $headerColors = [
        'available' => '#FFEB3B', // Yellow
        'approved' => '#4CAF50', // Green
        'rejected' => '#F44336', // Red
    ];

    $buttonColors = [
        'available' => '#FFEB3B', // Yellow
        'approved' => '#4CAF50', // Green
        'rejected' => '#F44336', // Red
    ];

    $title = $titles[$type] ?? 'Notification';
    $message = $messages[$type] ?? 'No additional information available.';
    $headerColor = $headerColors[$type] ?? '#000';
    $buttonColor = $buttonColors[$type] ?? '#000';

    return '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($title) . '</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
        <div style="max-width: 600px; margin: 20px auto; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <h2 style="color: ' . $headerColor . '; text-align: center;">' . htmlspecialchars($title) . '</h2>
            <p>Dear User,</p>
            <p>' . $message . '</p>
            <p style="text-align: center;">
                <a href="http://10.1.7.100:7777/st042.site/library/login.php" style="padding: 10px 20px; background-color: ' . $buttonColor . '; color: #fff; text-decoration: none; border-radius: 5px;">Visit Library</a>
            </p>
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
        if (in_array($new_state, ['RETURNED_GOOD_CONDITION', 'RETURNED_POOR_CONDITION'])) {
            $currTransactionWithFeeQry = "SELECT * FROM `transactions` t WHERE t.id = $transaction_id AND t.due_date < NOW();";
            $currTransactionWithFee = myQuery($currTransactionWithFeeQry);
            if ($currTransactionWithFee && mysqli_num_rows($currTransactionWithFee) > 0) {
                $fee = 150;
                echo "
                    <div class='bg-white p-6 rounded shadow-lg text-center'>
                        <h2 class='text-xl font-bold mb-4'>Fee Confirmation</h2>
                        <p class='text-gray-700 mb-6'>This user needs to pay <span class='font-bold'>$$fee</span> to return $book_name. Do you want to proceed?</p>
                        <form method='POST' action='' class='space-y-4'>
                            <input type='hidden' name='confirm_fee' value='yes'>
                            <input type='hidden' name='transaction_id' value='$transaction_id'>
                            <input type='hidden' name='new_state' value='$new_state'>
                                                        
                                                        
                            <input type='hidden' name='book_name' value='$book_name'>
                            <input type='hidden' name='book_id' value='$book_id'>

                            <div class='space-x-4'>
                                <button type='submit' class='bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded'>Confirm</button>
                                <button type='submit' name='confirm_fee' value='no' class='bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded'>Cancel</button>
                            </div>
                        </form>
                    </div>
                    ";
                $isStatusUpdateRequired = false;

            }



        }

        $update_query = "UPDATE transactions SET t_state = '$new_state' WHERE id = $transaction_id";
        $result = null;
        if ($isStatusUpdateRequired)
            $result = myQuery($update_query);

        if ($result) {
            echo "<p>Transaction updated successfully!</p>";


            if (in_array($new_state, ['RETURNED_GOOD_CONDITION', 'RETURNED_POOR_CONDITION'])) {
                $update_query1 = "UPDATE books SET is_available = 1 WHERE id = $book_id";
                $result = myQuery($update_query1);
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

                        if (sendMail($user_email, $subject, generateEmailContent($book_name, 'available'))) {
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


            } else if ($new_state === 'APPROVED') {
                // Fetch the user email and book name
                $email_query = "
                    SELECT u.email AS user_email, b.name AS book_name
                    FROM transactions t
                    JOIN users u ON t.user_id = u.id
                    JOIN books b ON t.book_id = b.id
                    WHERE t.id = $transaction_id
                ";
                $email_result = myQuery($email_query);

                if ($email_result && $email_row = mysqli_fetch_assoc($email_result)) {
                    $user_email = $email_row['user_email'];
                    $book_name = $email_row['book_name'];

                    // Generate email content
                    $subject = "Book Request Approved";
                    $email_content = generateEmailContent($book_name, 'approved');

                    // Send the email
                    if (sendMail($user_email, $subject, $email_content)) {
                        echo "<p>Email notification sent to $user_email.</p>";
                    } else {
                        echo "<p>Failed to send email to $user_email.</p>";
                        error_log("Email sending failed for $user_email (Transaction ID: $transaction_id).");
                    }
                } else {
                    echo "<p>Failed to fetch user email or book name for Transaction ID: $transaction_id.</p>";
                    error_log("Email or book name not found for Transaction ID: $transaction_id.");
                }
            } else if ($new_state === 'REJECTED') {
                // Fetch the user email and book name
                $email_query = "
                    SELECT u.email AS user_email, b.name AS book_name
                    FROM transactions t
                    JOIN users u ON t.user_id = u.id
                    JOIN books b ON t.book_id = b.id
                    WHERE t.id = $transaction_id
                ";
                $email_result = myQuery($email_query);

                if ($email_result && $email_row = mysqli_fetch_assoc($email_result)) {
                    $user_email = $email_row['user_email'];
                    $book_name = $email_row['book_name'];

                    // Generate email content
                    $subject = "Book Request Rejected";
                    $email_content = generateEmailContent($book_name, 'rejected');

                    // Send the email
                    if (sendMail($user_email, $subject, $email_content)) {
                        echo "<p>Email notification sent to $user_email.</p>";
                    } else {
                        echo "<p>Failed to send email to $user_email.</p>";
                        error_log("Email sending failed for $user_email (Transaction ID: $transaction_id).");
                    }
                } else {
                    echo "<p>Failed to fetch user email or book name for Transaction ID: $transaction_id.</p>";
                    error_log("Email or book name not found for Transaction ID: $transaction_id.");
                }
            }
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
                            <button type="submit" name="action" value="approve"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-500">Approve
                            </button>
                            <button type="submit" name="action" value="reject"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500">Reject
                            </button>
                        </form>
                    <?php elseif ($row['t_state'] === 'APPROVED'): ?>
                        <form method="POST" class="inline-block">
                            <input type="hidden" name="transaction_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="return_good"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-500">Return (Good
                                Condition)
                            </button>
                            <button type="submit" name="action" value="return_poor"
                                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-500">Return
                                (Poor Condition)
                            </button>
                            <button type="submit" name="action" value="not_returned"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500">Not Returned
                            </button>
                        </form>
                    <?php elseif ($row['t_state'] === 'NOT_RETURNED'): ?>
                        <form method="POST" class="inline-block">
                            <input type="hidden" name="transaction_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="return_good"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-500">Return (Good
                                Condition)
                            </button>
                            <button type="submit" name="action" value="return_poor"
                                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-500">Return
                                (Poor Condition)
                            </button>
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
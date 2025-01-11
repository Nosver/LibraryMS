<?php
session_start();
require 'connect.php';
$pageTitle = 'Profile';
require 'header.php';

$error = "";
$success = "";
$user = null;
$transactions = [];
$books = [];
$due_books = [];

if (!isset($_SESSION['user']['id'])) {
    header("Location: library/login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        try {
            $query = "SELECT password FROM users WHERE id = $userId";
            $result = myQuery($query);
            if ($result && $row = mysqli_fetch_assoc($result)) {
                $hashed_password = $row['password'];
                if (!password_verify($current_password, $hashed_password)) {
                    throw new Exception("Current password is incorrect.");
                }
            } else {
                throw new Exception("User not found.");
            }

            if ($new_password !== $confirm_password) {
                throw new Exception("New passwords do not match.");
            }

            if (strlen($new_password) < 6) {
                throw new Exception("New password must be at least 6 characters long.");
            }

            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = '$new_hashed_password' WHERE id = $userId";
            if (!myQuery($update_query)) {
                throw new Exception("Failed to update password.");
            }

            $success = "Password updated successfully.";
        } catch (Exception $e) {
            $error = "Error: " . htmlspecialchars($e->getMessage());
        }
    } elseif (isset($_POST['update_profile'])) {
        $new_username = $_POST['new_username'] ?? '';
        $new_email = $_POST['new_email'] ?? '';

        try {
            if (empty($new_username) || empty($new_email)) {
                throw new Exception("Username and email cannot be empty.");
            }

            if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format.");
            }

            $update_query = "UPDATE users SET username = '$new_username', email = '$new_email' WHERE id = $userId";
            if (!myQuery($update_query)) {
                throw new Exception("Failed to update profile.");
            }

            $_SESSION['user']['username'] = $new_username;
            $_SESSION['user']['email'] = $new_email;
            $success = "Profile updated successfully.";
        } catch (Exception $e) {
            $error = "Error: " . htmlspecialchars($e->getMessage());
        }
    }
}

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

    $queryDueBooks = "SELECT b.name, b.author, t.due_date, DATEDIFF(t.due_date, CURDATE()) AS days_left
                      FROM transactions t
                      JOIN books b ON t.book_id = b.id
                      WHERE t.user_id = $userId 
                        AND t.return_date IS NULL 
                        AND t.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 10 DAY)";
    $resultDueBooks = myQuery($queryDueBooks);

    while ($resultDueBooks && $row = mysqli_fetch_assoc($resultDueBooks)) {
        $due_books[] = $row;
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
        <?php elseif ($success): ?>
            <p class="text-green-500 text-sm mb-4"><?= $success ?></p>
        <?php endif; ?>
        <p class="text-lg mb-2"><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p class="text-lg mb-2"><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p class="text-lg mb-2"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <button id="updatePasswordBtn" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Password</button>
        <button id="updateProfileBtn" class="mt-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Update Profile</button>
    </div>

<?php if($_SESSION['user']['role']=='CUSTOMER'): ?>
        <!-- My Due Books Section -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-700">My Due Books (Due within 10 Days)</h2>
            <?php if (empty($due_books)): ?>
                <p class="text-gray-500">You have no books due within the next 10 days.</p>
            <?php else: ?>
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 border">Book Name</th>
                            <th class="px-4 py-2 border">Author</th>
                            <th class="px-4 py-2 border">Due Date</th>
                            <th class="px-4 py-2 border">Days Left</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($due_books as $book): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 border"><?= htmlspecialchars($book['name']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($book['author']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($book['due_date']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($book['days_left']) ?></td>
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
<?php endif; ?>

    </div>


    <!-- Update Password Modal -->
    <div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white rounded-lg w-96 p-6">
            <h2 class="text-2xl font-bold mb-4">Update Password</h2>
            <form method="POST" action="">
                <input type="hidden" name="update_password" value="1">
                <div class="mb-4">
                    <label class="block text-gray-700">Current Password</label>
                    <input type="password" name="current_password" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">New Password</label>
                    <input type="password" name="new_password" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Confirm New Password</label>
                    <input type="password" name="confirm_password" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Profile Modal -->
    <div id="profileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white rounded-lg w-96 p-6">
            <h2 class="text-2xl font-bold mb-4">Update Profile</h2>
            <form method="POST" action="">
                <input type="hidden" name="update_profile" value="1">
                <div class="mb-4">
                    <label class="block text-gray-700">New Username</label>
                    <input type="text" name="new_username" value="<?= htmlspecialchars($user['username']) ?>" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">New Email</label>
                    <input type="email" name="new_email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const updatePasswordBtn = document.getElementById('updatePasswordBtn');
    const passwordModal = document.getElementById('passwordModal');
    const updateProfileBtn = document.getElementById('updateProfileBtn');
    const profileModal = document.getElementById('profileModal');

    updatePasswordBtn.addEventListener('click', () => {
        passwordModal.classList.remove('hidden');
    });

    updateProfileBtn.addEventListener('click', () => {
        profileModal.classList.remove('hidden');
    });

    window.addEventListener('click', (e) => {
        if (e.target === passwordModal) {
            passwordModal.classList.add('hidden');
        }
        if (e.target === profileModal) {
            profileModal.classList.add('hidden');
        }
    });
</script>



</body>
</html>

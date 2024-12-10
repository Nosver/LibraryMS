<?php
// TODO: display if notification request is taken before
session_start();
require("connect.php");
require("header.php");
if (isset($_GET['book_id'])) {
    $bookId = $_GET['book_id'];
} else {
    header("Location: library/login.php"); // Redirect if no book_id
}

$qry = "SELECT * FROM books WHERE id=" . $bookId;
$res = myQuery($qry);

if ($res && mysqli_num_rows($res) > 0) {
    $result = mysqli_fetch_assoc($res);
} else {
    die("Book not found");
}

$ifBookedByCurrUser = false;
if (isset($_SESSION['user']['id'])) {
    $ifBookedByCurrUserQry = "
    SELECT * 
    FROM transactions 
    WHERE user_id = " . $_SESSION['user']['id'] . " 
    AND book_id = " . $bookId . " 
    AND t_state IN ('APPROVED', 'WAITING_APPROVAL', 'NOT_RETURNED');";
    $r = myQuery($ifBookedByCurrUserQry);
    if ($r && mysqli_num_rows($r) > 0) {
        $ifBookedByCurrUser = true;
    }
}
$IsNotificationRequestedBefore=false;

$IsNotificationRequestedBeforeQry="SELECT * FROM notifications WHERE user_id = " . $_SESSION['user']['id'] . " 
AND book_id = " . $bookId . " AND sent_at is NULL";

$out= myQuery($IsNotificationRequestedBeforeQry);
if ($out && mysqli_num_rows($out) > 0) {
    $IsNotificationRequestedBefore = true;
}
$IsNotificationRequested=false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'], $_POST['book_id'])) {
        $action = $_POST['action'];
        $bookId = intval($_POST['book_id']);
        if (!isset($_SESSION['user']['id'])) {
            header("Location: library/login.php");
            exit;
        }
        $id=$_SESSION['user']['id'];

        if ($action === 'reserve') {
            $updateQuery = "UPDATE books SET is_available = 0 WHERE id = $bookId";
            myQuery($updateQuery);
            $insertTransactionQuery = "INSERT INTO `transactions` 
            (`id`, `user_id`, `book_id`, `borrowed_at`, `due_date`, `return_date`, `fine_fee`, `t_state`) 
             VALUES 
            (NULL, '$id', '$bookId', NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), NULL, '0', 'WAITING_APPROVAL')";
            myQuery($insertTransactionQuery);
            $ifBookedByCurrUser = true;
            $successMessage= "<div class='text-green-500 mt-4'>Book reserved successfully!</div>";
        } elseif ($action === 'notify') {
            $addNotificationQuery="INSERT INTO `notifications` (`id`, `book_id`, `user_id`, `created_at`, `sent_at`) VALUES (NULL, '$bookId', '$id', NOW(), NULL);";
            myQuery($addNotificationQuery);
            $IsNotificationRequested=true;
            $successMessage= "<div class='text-blue-500 mt-4'>You will be notified when the book becomes available.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="max-w-screen-xl mx-auto p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <img class="w-full h-full object-cover" src="<?php echo $result['img_path']; ?>" alt="Book Image">
        </div>
        <div class="bg-white p-6 shadow-lg rounded-lg">
            <h2 class="text-3xl font-semibold text-gray-800"><?php echo($result['name']); ?></h2>
            <p class="text-gray-600 text-sm mb-4"><?php echo($result['description']); ?></p>

            <div class="mb-4">
                <span class="font-semibold text-gray-700">Author:</span>
                <span class="text-gray-600"><?php echo($result['author']); ?></span>
            </div>
            <div class="mb-4">
                <span class="font-semibold text-gray-700">ISBN:</span>
                <span class="text-gray-600"><?php echo($result['ISBN']); ?></span>
            </div>
            <div class="mb-4">
                <span class="font-semibold text-gray-700">Availability:</span>
                <span class="text-gray-600"><?php echo $result['is_available'] == 1 ? 'Available' : 'Not Available'; ?></span>
            </div>
            <div class="mb-4">
                <span class="font-semibold text-gray-700">Page count:</span>
                <span class="text-gray-600"><?php echo($result['page_number']); ?></span>
            </div>
            <div class="mb-4">
                <span class="font-semibold text-gray-700">Category:</span>
                <span class="text-gray-600"><?php echo($result['category']); ?></span>
            </div>
            <?php
            if( $successMessage){
                echo  $successMessage;
            }

            ?>

            <?php if (!$ifBookedByCurrUser && !$IsNotificationRequestedBefore) { ?>
                <div class="mt-6">
                    <form method="POST" action="">
                        <input type="hidden" name="book_id" value="<?php echo $bookId; ?>">
                        <button
                                type="submit"
                                name="action"
                                value="<?php echo $result['is_available'] == 1 ? 'reserve' : 'notify'; ?>"
                                class="w-full py-3 rounded-lg text-white font-semibold transition duration-200
                    <?php echo $result['is_available'] == 1 ? 'bg-blue-600 hover:bg-blue-500' : 'bg-gray-500 hover:bg-gray-400'; ?>">
                            <?php echo $result['is_available'] == 1 ? 'Reserve' : 'Notify me when available'; ?>
                        </button>
                    </form>
                </div>
            <?php } else if ($ifBookedByCurrUser) { ?>
                <div class="mt-6">
                    <button
                            disabled
                            class="w-full py-3 rounded-lg text-gray-500 bg-gray-300 cursor-not-allowed font-semibold">
                        Already Booked
                    </button>
                </div>
            <?php } else if ($IsNotificationRequestedBefore) { ?>
                <div class="mt-6">
                    <button
                            disabled
                            class="w-full py-3 rounded-lg text-gray-500 bg-gray-300 cursor-not-allowed font-semibold">
                        You will be notified when book is available
                    </button>
                </div>
            <?php } ?>


        </div>
    </div>
</div>
</body>
</html>

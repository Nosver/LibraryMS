<?php
require("connect.php");

if(isset($_GET['book_id'])) {
    $bookId=$_GET['book_id'];

} else{
    header("Location: login/staffTransactions.php"); // başka bir şey de yapılailir 404 sayfası falan
}

$qry="SELECT * FROM books WHERE id=".$bookId;

$res=myQuery($qry);

if ($res && mysqli_num_rows($res) > 0) {
    $result = mysqli_fetch_assoc($res); 
} else {
    die("Book not found"); 
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'], $_POST['book_id'])) {
        $action = $_POST['action'];
        $bookId = intval($_POST['book_id']);
        if (!isset($_SESSION['user']['id'])) {
            header("Location: library/login.php");
            exit;
        }
        if ($action === 'reserve') {
            
            $updateQuery = "UPDATE books SET is_available = 0 WHERE id = $bookId";
            myQuery($updateQuery);
            $result['is_available']=0;

            echo "<div class='text-green-500'>Book reserved successfully!</div>";
        } elseif ($action === 'notify') {
            // Code to handle notification logic
            // Example:
            echo "<div class='text-blue-500'>You will be notified when the book becomes available.</div>";
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
<body>
<div class="bg-gray-100 dark:bg-gray-800 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row -mx-4">
            <div class="md:flex-1 px-4">
                <div class="h-[460px] rounded-lg bg-gray-300 dark:bg-gray-700 mb-4">
                    <img class="w-full h-full object-cover" <?php echo "src='".$result['img_path']."'" ?>  alt="Product Image">
                </div>
                
            </div>
            <div class="md:flex-1 px-4">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2"><?php echo($result['name'])?></h2>
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                    <?php echo($result['description'])?>
                </p>
                <div class="mb-4">
                    <span class="font-bold text-gray-700 dark:text-gray-300">Author:</span>
                    <span class="text-gray-600 dark:text-gray-300"> <?php echo($result['author'])?></span> 
                </div>
                <div class="mb-4">
                    <span class="font-bold text-gray-700 dark:text-gray-300">ISBN:</span>
                    <span class="text-gray-600 dark:text-gray-300"> <?php echo($result['ISBN'])?></span> 
                </div>
                <div class="flex mb-4">
                    <div>
                        <span class="font-bold text-gray-700 dark:text-gray-300">Availability:</span>
                        <span class="text-gray-600 dark:text-gray-300"><?php
                        if ($result['is_available'] == 1) {
                            echo "Available";
                        } else {
                            echo "Not Available";
                        }
                        ?></span> 
                    </div>
                </div>
                <div class="mb-4">
                    <span class="font-bold text-gray-700 dark:text-gray-300">Page count:</span>
                    <span class="text-gray-600 dark:text-gray-300"> <?php echo($result['page_number'])?></span> 
                </div>
                <div class="mb-4">
                    <span class="font-bold text-gray-700 dark:text-gray-300">Category:</span>
                    <span class="text-gray-600 dark:text-gray-300"> <?php echo($result['category'])?></span> 
                </div>
                <div class="flex -mx-2 mb-4">
                    <div class="w-1/2 px-2"> 
                        <form method="POST" action="">
                        <input type="hidden" name="book_id" value="<?php echo $bookId; ?>">
                        <button 
                            type="submit" 
                            name="action" 
                            value="<?php echo $result['is_available'] == 1 ? 'reserve' : 'notify'; ?>" 
                            class="w-full bg-gray-900 dark:bg-gray-600 text-white py-2 px-4 rounded-full font-bold hover:bg-gray-800 dark:hover:bg-gray-700">
                        <?php
                        if ($result['is_available'] == 1) {
                            echo "Reserve";
                        } else {
                            echo "Notify me when available";
                        }
                        
                        ?>
                        
                        </button>
                        </form>
                    </div>
                    
                </div>
                
            </div>
        </div>
    </div>
</div>

</body>

<?php
session_start();
require 'connect.php';
$pageTitle = 'Update Book';
require 'header.php';

$error = "";
$success = "";

$id = $_GET['id'] ?? '';

if ($id) {
    
    $query = "SELECT * FROM books WHERE id = $id";
    $result = myQuery($query);
    $book = mysqli_fetch_assoc($result);
    
    if (!$book) {
        $error = "Book not found.";
    }
} else {
    $error = "Invalid book ID.";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $ISBN = $_POST['ISBN'] ?? '';
    $author = $_POST['author'] ?? '';
    $page_number = $_POST['page_number'] ?? null;
    $category = $_POST['category'] ?? '';
    $location = $_POST['location'] ?? '';
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $description = $_POST['description'] ?? '';

    
    $img_path = $book['img_path']; 
    if (!empty($_FILES['img_path']['name'])) {
        $uploadDir = 'images/';
        $uploadFile = $uploadDir . basename($_FILES['img_path']['name']);

        if (move_uploaded_file($_FILES['img_path']['tmp_name'], $uploadFile)) {
            $img_path = $uploadFile; 
        } else {
            $error = "An error occurred while uploading the image.";
        }
    }

    if (!$error) {
        $query = "UPDATE books SET 
                  name = '$name', 
                  ISBN = '$ISBN', 
                  author = '$author', 
                  page_number = $page_number, 
                  category = '$category', 
                  location = '$location', 
                  is_available = '$is_available', 
                  description = '$description', 
                  img_path = '$img_path' 
                  WHERE id = $id";

        if (myQuery($query)) {
            $success = "Book successfully updated.";
            header("Location: library/staff.php"); 
            exit;
        } else {
            $error = "An error occurred while updating the book.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Update Book</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-12 px-6">
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-3xl font-bold mb-4 text-gray-700">Update Book</h2>
            <?php if ($error): ?>
                <p class="text-red-500 text-sm mb-4"><?= $error ?></p>
            <?php elseif ($success): ?>
                <p class="text-green-500 text-sm mb-4"><?= $success ?></p>
            <?php endif; ?>
            <?php if ($book): ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-gray-700">Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($book['name']) ?>" required class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">ISBN</label>
                        <input type="text" name="ISBN" value="<?= htmlspecialchars($book['ISBN']) ?>" class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Author</label>
                        <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Page Number</label>
                        <input type="number" name="page_number" value="<?= htmlspecialchars($book['page_number']) ?>" class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Category</label>
                        <select name="category" class="w-full px-3 py-2 border rounded">
                            <option value="THRILLER" <?= $book['category'] == 'THRILLER' ? 'selected' : '' ?>>Thriller</option>
                            <option value="MYSTERY" <?= $book['category'] == 'MYSTERY' ? 'selected' : '' ?>>Mystery</option>
                            <option value="SCIFI" <?= $book['category'] == 'SCIFI' ? 'selected' : '' ?>>Sci-Fi</option>
                            <option value="SPORT" <?= $book['category'] == 'SPORT' ? 'selected' : '' ?>>Sport</option>
                            <option value="HISTORY" <?= $book['category'] == 'HISTORY' ? 'selected' : '' ?>>History</option>
                            <option value="ASTROLOGY" <?= $book['category'] == 'ASTROLOGY' ? 'selected' : '' ?>>Astrology</option>
                            <option value="PERSONAL_IMPROVEMENT" <?= $book['category'] == 'PERSONAL_IMPROVEMENT' ? 'selected' : '' ?>>Personal Improvement</option>
                            <option value="PSYCHOLOGY" <?= $book['category'] == 'PSYCHOLOGY' ? 'selected' : '' ?>>Psychology</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Location</label>
                        <input type="text" name="location" value="<?= htmlspecialchars($book['location']) ?>" class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Availability</label>
                        <input type="checkbox" name="is_available" <?= $book['is_available'] ? 'checked' : '' ?> class="mr-2"> Available
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Description</label>
                        <textarea name="description" class="w-full px-3 py-2 border rounded"><?= htmlspecialchars($book['description']) ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Image</label>
                        <input type="file" name="img_path" class="w-full px-3 py-2 border rounded">
                        <?php if ($book['img_path']): ?>
                            <p class="text-gray-500 text-sm mt-2">Current Image: <a href="<?= htmlspecialchars($book['img_path']) ?>" target="_blank">View</a></p>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

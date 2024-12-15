<?php
session_start();
require 'connect.php';
$pageTitle = 'Staff Management';
require 'header.php';

$error = "";
$success = "";
$book = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //echo '<pre>';
    //print_r($_FILES['img_path']);
    //echo '</pre>';
    if (isset($_POST['add_book'])) {
        $name = $_POST['name'] ?? '';
        $isbn = $_POST['isbn'] ?? '';
        $author = $_POST['author'] ?? '';
        $page_number = $_POST['page_number'] ?? null;
        $category = $_POST['category'] ?? '';
        $location = $_POST['location'] ?? '';
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        $description = $_POST['description'] ?? '';
        
        
    if (!empty($_FILES['img_path']['name'])) {
        $uploadDir = 'images/';
        $uploadFile = $uploadDir . basename($_FILES['img_path']['name']);
        if (move_uploaded_file($_FILES['img_path']['tmp_name'], $uploadFile)) {
            $img_path = $uploadFile; 
        } else {
            $error = "An error occurred while uploading the image.";
        }
    }

        
        $query = "INSERT INTO books (name, isbn, author, page_number, category, location, is_available, description, img_path) 
                  VALUES ('$name', '$isbn', '$author', '$page_number', '$category', '$location', '$is_available', '$description', '$img_path')";
        if (myQuery($query)) {
            $success = "Book added successfully.";
        } else {
            $error = "Failed to add book.";
        }
    }  elseif (isset($_POST['update_book'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $isbn = $_POST['isbn'];
        $author = $_POST['author'];
        $page_number = $_POST['page_number'];
        $category = $_POST['category'];
        $location = $_POST['location'];
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        $description = $_POST['description'];
        $img_path = $_POST['img_path'];

        $query = "UPDATE books SET 
                  name = '$name', 
                  isbn = '$isbn', 
                  author = '$author', 
                  page_number = '$page_number', 
                  category = '$category', 
                  location = '$location', 
                  is_available = '$is_available', 
                  description = '$description', 
                  img_path = '$img_path'
                  WHERE id = $id";
        
        if (myQuery($query)) {
            $success = "Book updated successfully.";
        } else {
            $error = "Failed to update book.";
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book'])) {
    $id = $_POST['id'] ?? '';
   
    if ($id) {
        
        $queryBooks = "DELETE FROM books WHERE id = $id";
        if (myQuery($queryBooks)) {
            $success = "Book and related notifications deleted successfully.";
        } else {
            $error = "Failed to delete book.";
        }
    } else {
        $error = "Invalid ID provided.";
    }
}
}


$queryBook = "SELECT * FROM books";
$resultBook = myQuery($queryBook);

while ($row = mysqli_fetch_assoc($resultBook)) {
    $book[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Staff Management</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-12 px-6">
        <!-- Add Books Section -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-3xl font-bold mb-4 text-gray-700">Add New Books</h2>
            <?php if ($error): ?>
                <p class="text-red-500 text-sm mb-4"><?= $error ?></p>
            <?php elseif ($success): ?>
                <p class="text-green-500 text-sm mb-4"><?= $success ?></p>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_book" value="1">
                <div class="mb-4">
                    <label class="block text-gray-700">Name</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ISBN</label>
                    <input type="text" name="isbn" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Author</label>
                    <input type="text" name="author" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Page Number</label>
                    <input type="number" name="page_number" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Category</label>
                    <select name="category" class="w-full px-3 py-2 border rounded">
                        <option value="THRILLER">Thriller</option>
                        <option value="MYSTERY">Mystery</option>
                        <option value="SCIFI">Sci-Fi</option>
                        <option value="SPORT">Sport</option>
                        <option value="HISTORY">History</option>
                        <option value="ASTROLOGY">Astrology</option>
                        <option value="PERSONAL_IMPROVEMENT	">Personel_Improvement</option>
                        <option value="PSYCHOLOGY">Psychology</option>
                        

                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Location</label>
                    <input type="text" name="location" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Availability</label>
                    <input type="checkbox" name="is_available" class="mr-2"> Available
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Description</label>
                    <textarea name="description" class="w-full px-3 py-2 border rounded"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Image</label>
                    <input type="file" name="img_path" class="w-full px-3 py-2 border rounded">
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Books</button>
            </form>
        </div>

        <!-- Book List Section -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-700">Book List</h2>
            <?php if (empty($book)): ?>
                <p class="text-gray-500">No book found.</p>
            <?php else: ?>
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 border">ID</th>
                            <th class="px-4 py-2 border">Name</th>
                            <th class="px-4 py-2 border">Category</th>
                            <th class="px-4 py-2 border">Location</th>
                            <th class="px-4 py-2 border">Availability</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($book as $member): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 border"><?= htmlspecialchars($member['id']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($member['name']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($member['category']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($member['location']) ?></td>
                                <td class="px-4 py-2 border"><?= $member['is_available'] ? 'Available' : 'Not Available' ?></td>
                                <td class="px-4 py-2 border">
                                   <form method="GET" action="update_book.php" style="display:inline;">
                                     <input type="hidden" name="id" value="<?= $member['id'] ?>">
                                     <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-3 rounded">Update</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $member['id'] ?>">
                                        <button type="submit" name="delete_book" class="bg-red-500 hover:bg-red-700 text-white py-1 px-3 rounded">Delete</button>
                                    </form>

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
<?php
require 'connect.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$books_per_page = 10;
$offset = ($page - 1) * $books_per_page;

$where_clauses = [];
if ($category) {
    $where_clauses[] = "category = '$category'";
}
if ($search) {
    $where_clauses[] = "(name LIKE '%$search%' OR author LIKE '%$search%')";
}
$where_sql = $where_clauses ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$total_books_query = "SELECT COUNT(*) as total FROM books $where_sql";
$total_books_result = myQuery($total_books_query);
$total_books_row = mysqli_fetch_assoc($total_books_result);
$total_books = $total_books_row['total'];

$sql = "SELECT * FROM books $where_sql LIMIT $books_per_page OFFSET $offset";
$result = myQuery($sql);

$total_pages = ceil($total_books / $books_per_page);

$category_query = "SELECT DISTINCT category FROM books";
$category_result = myQuery($category_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Catalog</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white">

<section class="bg-white">
    <div class="px-2 mb-12 mx-auto py-8 max-w-4xl">
        <!-- Filter and Search -->
        <form method="GET" action="" class="flex justify-between items-center mb-12">
            <!-- Filter Dropdown -->
            <div>
                <label for="category" class="text-gray-700">Filter by Category:</label>
                <select name="category" id="category" class="px-2 py-1 border rounded">
                    <option value="">All</option>
                    <?php while ($row = mysqli_fetch_assoc($category_result)): ?>
                        <option value="<?php echo htmlspecialchars($row['category']); ?>" 
                            <?php echo ($category === $row['category']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <!-- Search Input -->
            <div>
                <label for="search" class="text-gray-700">Search:</label>
                <input type="text" name="search" id="search" 
                       value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Search by name or author" 
                       class="px-2 py-1 border rounded">
                <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Apply</button>
            </div>
        </form>

        <!-- Book list -->

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 justify-items-center">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <article class="flex border rounded max-w-md">
                        <img class="object-cover w-[175px] h-[250px] rounded-l" 
                             loading="lazy" 
                             src="<?php echo htmlspecialchars($row['img_path'] ?? 'https://via.placeholder.com/175x250'); ?>" 
                             alt="<?php echo htmlspecialchars($row['name']); ?>" 
                             width="175" 
                             height="250">
                        <div class="flex flex-col justify-between flex-1 p-2">
                            <div class="flex flex-col gap-2">
                                <h3 class="font-semibold line-clamp-2"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p class="text-sm text-gray-700 line-clamp-3">
                                    <?php echo htmlspecialchars($row['description'] ?? 'No description available.'); ?>
                                </p>
                                <p class="text-sm text-gray-600">-<?php echo htmlspecialchars($row['author']); ?></p>
                            </div>
                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                <a 
                                    <?php echo "href='bookDetails.php?book_id=".$row['id']."'"; ?> 
                                    class="flex items-center gap-1 px-2 py-1 border rounded">
                                    <img src="https://www.svgrepo.com/show/162476/flipkart.svg" class="w-5 h-5" alt="" width="20" height="20">
                                    <span>Details</span>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">No books found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Pagination -->
<div class="flex justify-center space-x-2 my-4">
    <?php if ($page > 1): ?>
        <a href="?category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>" 
           class="px-4 py-2 bg-gray-200 rounded">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>" 
           class="px-4 py-2 <?php echo $i == $page ? 'bg-blue-500 text-white' : 'bg-gray-200'; ?> rounded"><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>" 
           class="px-4 py-2 bg-gray-200 rounded">Next</a>
    <?php endif; ?>
</div>

</body>
</html>

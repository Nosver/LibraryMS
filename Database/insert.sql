-- Insert example data for library_nosver database

USE library_nosver;

-- Insert users
INSERT INTO `users` (`id`, `role`, `username`, `email`, `password`, `verification_url`) VALUES
(53, 'CUSTOMER', 'alice_wonder', 'alice@example.com', '<hashed_password1>', 'verify_53_alice'),
(54, 'STAFF', 'bob_builder', 'bob@example.com', '<hashed_password2>', 'verify_54_bob'),
(55, 'CUSTOMER', 'charlie_day', 'charlie@example.com', '<hashed_password3>', 'verify_55_charlie');

-- Insert books
INSERT INTO `books` (`id`, `name`, `ISBN`, `author`, `page_number`, `category`, `location`, `is_available`, `description`, `img_path`) VALUES
(37, 'To Kill a Mockingbird', '9780061120084', 'Harper Lee', 281, 'MYSTERY', 'Shelf F1', 1, 'A novel about the serious issues of rape and racial inequality.', 'images/book_cover_175x250.png'),
(38, 'The Hobbit', '9780547928227', 'J.R.R. Tolkien', 310, 'SCIFI', 'Shelf F2', 1, 'A fantasy novel and children\'s book by English author J. R. R. Tolkien.', 'images/book_cover_175x250.png');

-- Insert transactions
INSERT INTO `transactions` (`id`, `user_id`, `book_id`, `borrowed_at`, `due_date`, `return_date`, `fine_fee`, `t_state`) VALUES
(2, 53, 37, '2024-12-10 09:00:00', '2024-12-24 09:00:00', NULL, 0, 'APPROVED'),
(3, 54, 38, '2024-12-11 10:30:00', '2024-12-25 10:30:00', NULL, 0, 'WAITING_APPROVAL');

-- Insert notifications
INSERT INTO `notifications` (`id`, `book_id`, `user_id`, `created_at`, `sent_at`) VALUES
(1, 37, 53, '2024-12-10 09:05:00', NULL),
(2, 38, 54, '2024-12-11 10:35:00', NULL);
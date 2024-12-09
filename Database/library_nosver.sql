-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 09, 2024 at 07:30 AM
-- Server version: 8.0.40-0ubuntu0.22.04.1
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_nosver`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `ISBN` text,
  `author` text,
  `page_number` int DEFAULT NULL,
  `category` enum('THRILLER','MYSTERY','SCIFI','SPORT','PERSONAL_IMPROVEMENT','HISTORY','COOKING','ASTROLOGY','PSYCHOLOGY','PHILOSOPHY','RELIGION') DEFAULT NULL,
  `location` text,
  `is_available` tinyint(1) DEFAULT NULL,
  `description` text,
  `img_path` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `name`, `ISBN`, `author`, `page_number`, `category`, `location`, `is_available`, `description`, `img_path`) VALUES
(1, 'Fantasia', '213111213', 'John Doe', 100, 'THRILLER', 'csvsdsdv', 0, 'awesome book', 'images/book_cover_175x250.png'),
(2, 'The Art of Programming', '123-4567890123', 'Donald Knuth', 500, 'THRILLER', 'Shelf A1', 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet vehicula sem et sagittis. Pellentesque ac condimentum dolor. Suspendisse volutpat.\n\n', 'images/book_cover_175x250.png'),
(3, 'The Mystery Island', '234-5678901234', 'Jules Verne', 320, 'PHILOSOPHY', 'Shelf B2', 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet vehicula sem et sagittis. Pellentesque ac condimentum dolor. Suspendisse volutpat.\n\n', 'images/book_cover_175x250.png'),
(4, 'World History', '345-6789012345', 'Howard Zinn', 450, 'HISTORY', 'Shelf C3', 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet vehicula sem et sagittis. Pellentesque ac condimentum dolor. Suspendisse volutpat.\n\n', 'images/book_cover_175x250.png'),
(5, 'Astrophysics for Beginners', '456-7890123456', 'Neil deGrasse Tyson', 350, 'HISTORY', 'Shelf D4', 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet vehicula sem et sagittis. Pellentesque ac condimentum dolor. Suspendisse volutpat.\n\n', 'images/book_cover_175x250.png'),
(6, 'Psychology of Dreams', '567-8901234567', 'Sigmund Freud', 300, 'PSYCHOLOGY', 'Shelf E5', 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet vehicula sem et sagittis. Pellentesque ac condimentum dolor. Suspendisse volutpat.\n\n', 'images/book_cover_175x250.png'),
(22, 'The Martian', '9780553418026', 'Andy Weir', 369, 'SCIFI', 'Shelf A1', 1, NULL, NULL),
(23, 'The Da Vinci Code', '9780307474278', 'Dan Brown', 689, 'MYSTERY', 'Shelf B2', 1, NULL, NULL),
(24, 'Thinking, Fast and Slow', '9780374533557', 'Daniel Kahneman', 512, 'PSYCHOLOGY', 'Shelf C3', 1, NULL, NULL),
(25, 'Sapiens: A Brief History of Humankind', '9780062316110', 'Yuval Noah Harari', 443, 'HISTORY', 'Shelf D4', 1, NULL, NULL),
(26, 'Astrophysics for People in a Hurry', '9780393609394', 'Neil deGrasse Tyson', 224, 'ASTROLOGY', 'Shelf E5', 1, NULL, NULL),
(27, 'The Silent Patient', '9781250301697', 'Alex Michaelides', 368, 'MYSTERY', 'Shelf A1', 1, NULL, NULL),
(28, 'Educated', '9780399590504', 'Tara Westover', 334, 'PERSONAL_IMPROVEMENT', 'Shelf B2', 1, NULL, NULL),
(29, 'Sapiens: A Brief History of Humankind', '9780062316097', 'Yuval Noah Harari', 443, 'HISTORY', 'Shelf C3', 1, NULL, NULL),
(30, 'Atomic Habits', '9780735211292', 'James Clear', 320, 'PERSONAL_IMPROVEMENT', 'Shelf D4', 1, NULL, NULL),
(31, 'The Psychology of Money', '9780857197689', 'Morgan Housel', 252, 'PSYCHOLOGY', 'Shelf E5', 1, NULL, NULL),
(32, 'Book 1', '978-3-16-148410-0', 'Author 1', 200, 'THRILLER', 'Shelf A', 1, NULL, NULL),
(33, 'Book 2', '978-0-123-45678-9', 'Author 2', 300, 'MYSTERY', 'Shelf B', 1, NULL, NULL),
(34, 'Book 3', '978-1-234-56789-0', 'Author 3', 400, 'SCIFI', 'Shelf C', 0, NULL, NULL),
(35, 'Book 4', '978-0-987-65432-1', 'Author 4', 500, 'HISTORY', 'Shelf D', 1, NULL, NULL),
(36, 'Book 5', '978-0-654-32109-8', 'Author 5', 600, 'PSYCHOLOGY', 'Shelf E', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `book_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `borrowed_at` timestamp NOT NULL,
  `due_date` timestamp NOT NULL,
  `return_date` timestamp NULL DEFAULT NULL,
  `fine_fee` float DEFAULT '0',
  `t_state` enum('WAITING_APPROVAL','APPROVED','REJECTED','DELIVERED','RETURNED_GOOD_CONDITION','RETURNED_POOR_CONDITION','NOT_RETURNED') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `book_id`, `borrowed_at`, `due_date`, `return_date`, `fine_fee`, `t_state`) VALUES
(1, 7, 1, '2024-12-01 10:00:00', '2024-12-15 10:00:00', NULL, 0, 'WAITING_APPROVAL');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `role` enum('CUSTOMER','STAFF') DEFAULT NULL,
  `username` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `password` text,
  `verification_url` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `username`, `email`, `password`, `verification_url`) VALUES
(1, 'CUSTOMER', 'JohnDoe', 'john@example.com', '<hashed_password>', 'verify_647bfc123e57b'),
(3, 'CUSTOMER', 'asd', 'ads@asdasd', 'asdasda', 'verify_6751e742f2cd92.17320930'),
(4, 'CUSTOMER', 'customer1234', 'customer1234@mail.com', '$2y$10$iEOwqYF0Rfe8TXPgo.WsN.isvSb.YPXIMGX52HuJaQ3WkGkP9OOdK', 'verify_675338e47d7ff7.52477719'),
(7, 'CUSTOMER', 'sa', 'sa@mail.com', '$2y$10$q2sGG/M9k1NlRApg5Q3HEexjY6EY0IUkIKtQ3qdEU/KUvZ/MnVG3m', 'verify_6754a7400d0bb3.37817319'),
(9, 'CUSTOMER', 'sad', 'as@asd', '$2y$10$UTYw2VLd8a.9WjXqPdJjmeJUs2TfIyKbYZNb2gb4cDVbnZPlTEUDi', 'verify_67568f4ace8e99.19995099'),
(10, 'CUSTOMER', 'asdfds', 'dfsdff@dasfad', '$2y$10$xm0XRgxIl0KJmSFK04qHDed68vIA023TvYPO9ycsDXAnZe9EoyY2e', 'verify_67568fdb365ec2.44944210'),
(11, 'CUSTOMER', 'kemal', 'kemal@mail', '$2y$10$EaN1hLxjtAMjmd.XGFxth.3/6SnoQggJobXrTHloJihE0xYMATc0O', 'verify_675692dfde46d9.03237171'),
(17, 'CUSTOMER', 'john_doe', 'john_doe1@example.com', 'hashedpassword123', 'verification-url-1'),
(18, 'STAFF', 'admin_user', 'admin_user@example.com', 'hashedpassword456', 'verification-url-2'),
(19, 'CUSTOMER', 'jane_doe', 'jane_doe2@example.com', 'hashedpassword789', 'verification-url-3'),
(20, 'CUSTOMER', 'mark_smith', 'mark_smith3@example.com', 'hashedpassword000', 'verification-url-4'),
(21, 'CUSTOMER', 'sarah_jones', 'sarah_jones4@example.com', 'hashedpassword111', 'verification-url-5'),
(32, 'CUSTOMER', 'alice_smith', 'alice.smith@example.com', 'hashedpassword1', 'verify_alice_1'),
(33, 'CUSTOMER', 'bob_johnson', 'bob.johnson@example.com', 'hashedpassword2', 'verify_bob_2'),
(34, 'STAFF', 'claire_brown', 'claire.brown@example.com', 'hashedpassword3', 'verify_claire_3'),
(35, 'CUSTOMER', 'david_wilson', 'david.wilson@example.com', 'hashedpassword4', 'verify_david_4'),
(36, 'STAFF', 'eva_martinez', 'eva.martinez@example.com', 'hashedpassword5', 'verify_eva_5'),
(37, 'CUSTOMER', 'dogukan', 'dogukan@mail.com', '$2y$10$BXuj.nq6oQibqAV0gWFfaet1PYudzPK3CHtgBPX5cDYm91KnTYCXu', 'verify_67569ac5c726b3.44465386'),
(48, 'CUSTOMER', 'jane_2doe', 'jane.doe123@example.com', 'hashed_2password_21', 'verify_2jane_21'),
(49, 'CUSTOMER', 'john_2doe', 'john.doe123@example.com', 'hashed_2password_22', 'verify_2john_22'),
(50, 'CUSTOMER', 'mary_2jones', 'mary.jones123@example.com', 'hashed_2password_23', 'verify_2mary_23'),
(51, 'CUSTOMER', 'michael_2brown', 'michael.brown123@example.com', 'hashed_2password_24', 'verify_2michael_24'),
(52, 'CUSTOMER', 'susan_2white', 'susan.white123@example.com', 'hashed_2password_25', 'verify_2susan_25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`,`email`),
  ADD UNIQUE KEY `username_2` (`username`,`email`),
  ADD UNIQUE KEY `username_3` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

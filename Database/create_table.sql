-- Drop existing tables if they exist
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `books`;
DROP TABLE IF EXISTS `users`;

-- Create tables for users, books, transactions, and notifications

CREATE TABLE `users` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `role` ENUM('CUSTOMER', 'STAFF'),
  `username` text,
  `email` text UNIQUE,  -- Ensure email is unique
  `password` text,
  `verification_url` text,
  UNIQUE (`id`)  -- Ensure the primary key (id) is unique
);

CREATE TABLE `books` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` text NOT NULL,
  `ISBN` text UNIQUE,  -- Ensure ISBN is unique
  `author` text,
  `page_number` int,
  `category` ENUM('THRILLER', 'MYSTERY', 'SCIFI', 'SPORT', 'PERSONAL_IMPROVEMENT', 'HISTORY', 'COOKING', 'ASTROLOGY', 'PSYCHOLOGY', 'PHILOSOPHY', 'RELIGION'),
  `location` text,
  `is_available` boolean,
  UNIQUE (`id`)  -- Ensure the primary key (id) is unique
);

CREATE TABLE `transactions` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `borrowed_at` timestamp NOT NULL,
  `due_date` timestamp NOT NULL,
  `return_date` timestamp DEFAULT null,
  `fine_fee` float DEFAULT 0,
  `t_state` ENUM('WAITING_APPROVAL', 'APPROVED', 'REJECTED', 'DELIVERED', 'RETURNED_GOOD_CONDITION', 'RETURNED_POOR_CONDITION', 'NOT_RETURNED') NOT NULL,
  UNIQUE (`id`),  -- Ensure the primary key (id) is unique
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  FOREIGN KEY (`book_id`) REFERENCES `books` (`id`)
);

CREATE TABLE `notifications` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `book_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp,
  `sent_at` timestamp DEFAULT null,
  UNIQUE (`id`),  -- Ensure the primary key (id) is unique
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  FOREIGN KEY (`book_id`) REFERENCES `books` (`id`)
);


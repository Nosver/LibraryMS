-- Create tables for the library database MySQL

CREATE TABLE `users` (
  `id` int PRIMARY KEY,
  `role` ENUM ('CUSTOMER', 'STAFF'),
  `username` text,
  `email` text,
  `password` text,
  `verification_url` text
);

CREATE TABLE `transactions` (
  `id` int PRIMARY KEY,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `borrowed_at` timestamp NOT NULL,
  `due_date` timestamp NOT NULL,
  `return_date` timestamp DEFAULT null,
  `fine_fee` float DEFAULT 0,
  `t_state` ENUM ('WAITING_APPROVAL', 'APPROVED', 'REJECTED', 'DELIVERED', 'RETURNED_GOOD_CONDITION', 'RETURNED_POOR_CONDITION', 'NOT_RETURNED') NOT NULL
);

CREATE TABLE `books` (
  `id` int PRIMARY KEY,
  `name` text NOT NULL,
  `ISBN` text,
  `author` text,
  `page_number` int,
  `category` ENUM ('THRILLER', 'MYSTERY', 'SCIFI', 'SPORT', 'PERSONAL_IMPROVEMENT', 'HISTORY', 'COOKING', 'ASTROLOGY', 'PSYCHOLOGY', 'PHILOSOPHY', 'RELIGION'),
  `location` text,
  `is_available` boolean
);

CREATE TABLE `notifications` (
  `id` int PRIMARY KEY,
  `book_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp,
  `sent_at` timestamp DEFAULT null
);

ALTER TABLE `transactions` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `transactions` ADD FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

ALTER TABLE `notifications` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `notifications` ADD FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

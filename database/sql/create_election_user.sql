-- SQL to create election_user pivot table (import via phpMyAdmin or mysql CLI)
-- Adjust database name if necessary. Run from the target database.

CREATE TABLE IF NOT EXISTS `election_user` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `election_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `election_user_election_id_user_id_unique` (`election_id`,`user_id`),
  KEY `election_user_election_id_index` (`election_id`),
  KEY `election_user_user_id_index` (`user_id`),
  CONSTRAINT `election_user_election_id_foreign` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `election_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

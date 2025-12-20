-- SQL fallback: add nullable user_id to candidates table
-- Run in the target database via phpMyAdmin or mysql CLI

ALTER TABLE `candidates`
ADD COLUMN IF NOT EXISTS `user_id` BIGINT UNSIGNED NULL AFTER `id`;

-- Add index and FK if users table exists
ALTER TABLE `candidates`
ADD INDEX IF NOT EXISTS `candidates_user_id_index` (`user_id`);

-- Add foreign key (if supported). If this fails, run without FK and add later.
ALTER TABLE `candidates`
ADD CONSTRAINT IF NOT EXISTS `candidates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

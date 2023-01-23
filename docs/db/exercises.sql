SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `exercises` (
`id` int(10) UNSIGNED NOT NULL,
`user_id` int(10) UNSIGNED NOT NULL,
`template_id` int(10) UNSIGNED DEFAULT NULL,
`title` varchar(50) COLLATE utf8mb4_bin NOT NULL,
`description` varchar(255) COLLATE utf8mb4_bin,
`url` varchar(255) COLLATE utf8mb4_bin,
`type_flag` tinyint(4) NOT NULL DEFAULT 0,
`subtype_flag` smallint(6) NOT NULL DEFAULT 0,
`language_flag` tinyint(4) NOT NULL DEFAULT 0,
`level_flag` tinyint(4) NOT NULL DEFAULT 0,
`program_id` int(10) UNSIGNED DEFAULT NULL,
`program_name` varchar(50) COLLATE utf8mb4_bin,
`program_count` smallint(6) NOT NULL DEFAULT 0,
`route` varchar(50) COLLATE utf8mb4_bin,
`frequency_period` tinyint(4) NOT NULL DEFAULT 0,
`frequency_reps` tinyint(4) NOT NULL DEFAULT 0,
`display_order` smallint(6) NOT NULL DEFAULT 0,
`action_flag` tinyint(4) NOT NULL DEFAULT 0,
`template_flag` tinyint(1) NOT NULL DEFAULT 0,
`active_flag` tinyint(1) NOT NULL DEFAULT 1,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE `exercises`
ADD PRIMARY KEY (`id`);

ALTER TABLE `exercises`
MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE exercises
ADD CONSTRAINT fk_user_id
FOREIGN KEY (user_id) REFERENCES users(id)

COMMIT;


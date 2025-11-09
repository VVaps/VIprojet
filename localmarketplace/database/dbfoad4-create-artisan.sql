


CREATE TABLE IF NOT EXISTS `artisans` (
	`id` BIGINT UNSIGNED AUTO_INCREMENT NOT NULL UNIQUE,
	`name` varchar(255) NOT NULL,
	`address` varchar(255) NOT NULL,
	`rib` varchar(255) NOT NULL,
	`id_user` BIGINT UNSIGNED NOT NULL,
	`created_at` datetime NOT NULL,
	`modified_at` datetime NOT NULL,
	`description` text,
	PRIMARY KEY (`id`)
);




ALTER TABLE `artisans` ADD CONSTRAINT `artisans_fk4` FOREIGN KEY (`id_user`) REFERENCES `users`(`id`);
ALTER TABLE `products` ADD CONSTRAINT `products_fk3` FOREIGN KEY (`artisan_id`) REFERENCES `artisans`(`id`);
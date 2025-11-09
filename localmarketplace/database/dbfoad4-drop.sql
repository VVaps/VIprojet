ALTER TABLE `order_line` DROP FOREIGN KEY `order_line_fk0`;
ALTER TABLE `order_line` DROP FOREIGN KEY `order_line_fk1`;
ALTER TABLE `order` DROP FOREIGN KEY `order_fk0`;

ALTER TABLE `artisans` DROP FOREIGN KEY `artisans_fk0`;
ALTER TABLE `products` DROP FOREIGN KEY `products_fk0`;
DROP TABLE IF EXISTS `order_line`;
DROP TABLE IF EXISTS `order`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `artisans`;
DROP TABLE IF EXISTS `products`;
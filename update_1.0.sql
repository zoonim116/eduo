CREATE TABLE `categories` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `texts` ADD `category_id` INT(11) NOT NULL AFTER `repository_id`;
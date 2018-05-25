ALTER TABLE `users` ADD `is_teacher` TINYINT(1) NOT NULL AFTER `password`;


ALTER TABLE `users` ADD `fb_user_id` INT(11) NOT NULL AFTER `is_teacher`, ADD `fb_access_token` TEXT NOT NULL AFTER `fb_user_id`;


CREATE TABLE `profile_tracking` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL , `profile_id` INT(11) NOT NULL , `created_at` INT(11) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

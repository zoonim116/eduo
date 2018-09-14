CREATE TABLE `notifications` ( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `type` ENUM('success','info','warning','error') NOT NULL , `from_user_id` INT UNSIGNED NOT NULL , `to_user_id` INT UNSIGNED NOT NULL , `text` TEXT NOT NULL , `created_at` INT UNSIGNED NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `lessons` ( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `repository_id` BIGINT UNSIGNED NOT NULL , `datetime` INT NOT NULL , `rating` TINYINT(2) NOT NULL , `note` TEXT NOT NULL , `user_id` INT NOT NULL , `created_at` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `schools` ( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `edoo`.`courses` ( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `datetime_from` INT NOT NULL , `datetime_to` INT NOT NULL , `number_of_lessons` TINYINT NOT NULL , `number_of_students` TINYINT NOT NULL , `school_id` BIGINT UNSIGNED NOT NULL , `repository_id` BIGINT UNSIGNED NOT NULL , `note` TEXT NOT NULL , `user_id` BIGINT UNSIGNED NOT NULL, `created_at` INT UNSIGNED NOT NULL  , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `users` ADD `phone` VARCHAR(50) NULL AFTER `password`;


ALTER TABLE `users` ADD `image` VARCHAR(255) NULL AFTER `phone`;


ALTER TABLE `users` ADD `code` VARCHAR(50) NULL AFTER `image`;

ALTER TABLE `users` ADD `file` VARCHAR(255) NULL AFTER `code`;
ALTER TABLE `users` ADD `status` VARCHAR(255) NULL AFTER `file`;
ALTER TABLE `users` ADD `verify` VARCHAR(255) NULL AFTER `status`;


------------------------------------------------

CREATE TABLE `laravel`.`usercategory` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NULL , `cat_id` INT NULL , `updated_at` DATETIME NULL , `created_at` DATETIME NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `review` ADD `review_id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`review_id`);


------------------------------------------------------------------------------------


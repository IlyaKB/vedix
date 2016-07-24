-- #39 2015.06.08 12:30:00

ALTER TABLE `mag_manufacturers` ADD `url_name` VARCHAR(32) NULL DEFAULT NULL AFTER `user_id`, ADD UNIQUE (`url_name`) ;
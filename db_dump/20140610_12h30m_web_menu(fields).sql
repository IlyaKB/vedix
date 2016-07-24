-- #20 2014.06.10 12:30:00

ALTER TABLE `web_menu` ADD `object_type` VARCHAR(16) NULL DEFAULT NULL AFTER `description`, ADD `object_id` INT NULL DEFAULT NULL AFTER `object_type`, ADD INDEX (`object_type`, `object_id`) ;
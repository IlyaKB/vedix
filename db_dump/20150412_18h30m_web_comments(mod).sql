-- #34 2015.04.12 18:30:00

ALTER TABLE `web_comments` CHANGE `entity_type` `entity_type` ENUM('page','news','state','blog') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
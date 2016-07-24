-- #35 2015.04.13 07:00:00

ALTER TABLE `web_votes` CHANGE `object_type` `object_type` ENUM('page','news','blog','state','poll') CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;
ALTER TABLE `web_poll` DROP `imgurlcode`;
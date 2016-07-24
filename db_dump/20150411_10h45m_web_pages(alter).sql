-- #33 2015.04.11 10:45:00

ALTER TABLE `web_pages` DROP `category_id`, DROP `doctype_id`, DROP `docnumber`, DROP `smallcaption`, DROP `fullcaption`, DROP `announce`, DROP `announceimg_id`, DROP `isincdesc`, DROP `isinckeyw`;
ALTER TABLE `web_pages` CHANGE `urlcaption` `url_name` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;
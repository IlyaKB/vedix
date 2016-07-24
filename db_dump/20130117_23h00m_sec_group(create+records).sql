-- #8 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `sec_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `number` smallint(6) DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `code` varchar(24) COLLATE utf8_bin NOT NULL,
  `isnotinuse` tinyint(1) DEFAULT NULL,
  `isshowerror` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

INSERT INTO `sec_group` (`id`, `parent_id`, `number`, `name`, `code`, `isnotinuse`, `isshowerror`) VALUES
(1, NULL, NULL, 'Root', 'root', NULL, 1),
(0, 1, 3, 'Guests', 'guests', NULL, NULL);

UPDATE sec_group SET id = 0 WHERE (id = 2);

INSERT INTO `sec_group` (`id`, `parent_id`, `number`, `name`, `code`, `isnotinuse`, `isshowerror`) VALUES
(2, 1, 1, 'Administrators', 'admins', NULL, 1),
(3, 1, 2, 'Authorizen users', 'users', NULL, NULL),
(99, 1, 13, 'Testers', 'testers', NULL, 1);

ALTER TABLE `sec_group` ORDER BY `id`;
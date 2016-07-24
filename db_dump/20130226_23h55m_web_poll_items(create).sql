-- #16 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `web_poll_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `number` smallint(6) DEFAULT NULL,
  `text` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `votes` INT(6) NOT NULL DEFAULT '0' COMMENT 'Количество отметок',
  `color` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
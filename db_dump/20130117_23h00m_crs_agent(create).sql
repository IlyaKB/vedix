-- #1 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `crs_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `browser_id` int(11) NOT NULL DEFAULT '0' COMMENT 'ИД группы браузеров',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `browser_id` (`browser_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Агенты' AUTO_INCREMENT=1 ;
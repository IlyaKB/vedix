-- #27 2014.10.27 09:30:00

CREATE TABLE IF NOT EXISTS `web_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_type` enum('page','news','state','post') DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `author_name` varchar(64) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `text` text,
  `counter_like` int(11) NOT NULL DEFAULT '0',
  `counter_dislike` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) DEFAULT NULL COMMENT 'NULL - требует модерации, 1 - ok, 0 - скрыт',
  PRIMARY KEY (`id`),
  KEY `entity_type` (`entity_type`,`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
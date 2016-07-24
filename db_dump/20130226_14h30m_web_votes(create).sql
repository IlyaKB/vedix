-- #14 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `web_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_type` enum('page','news','post','state','poll') CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `value` VARCHAR( 128 ) NULL DEFAULT NULL,
  `votetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='История оценок' AUTO_INCREMENT=1 ;
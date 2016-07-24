-- #10 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `sas_redirection_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) DEFAULT NULL,
  `godate` datetime DEFAULT NULL,
  `srcpage` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `destlink` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='История переадресаций (переходов на другие сайты)' AUTO_INCREMENT=1 ;
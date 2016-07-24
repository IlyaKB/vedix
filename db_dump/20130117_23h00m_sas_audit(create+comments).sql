-- #6 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `sas_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `rec_id` int(11) DEFAULT NULL,
  `key_id` int(11) DEFAULT NULL COMMENT 'Дополнительный ИД',
  `evdatetime` datetime NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `evtdetail_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `evtdetail_id` (`evtdetail_id`),
  KEY `evdatetime` (`evdatetime`),
  KEY `session_id` (`session_id`),
  KEY `class_id` (`class_id`),
  KEY `rec_id` (`rec_id`),
  KEY `key_id` (`key_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Подсистема SAS. Аудит работы с ИС.' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sas_audit_comment` (
  `audit_id` int(11) NOT NULL,
  `comment` text COLLATE utf8_bin,
  UNIQUE KEY `audit_id` (`audit_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `sas_redirection_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) DEFAULT NULL,
  `godate` datetime DEFAULT NULL,
  `srcpage` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `destlink` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='WEB. История переадресаций (переходов на другие сайты)' AUTO_INCREMENT=1 ;
-- #17 2013.11.11 12:15:00

CREATE TABLE IF NOT EXISTS `web_document_rights` (
  `document_type` enum('page','news','state','blog') COLLATE utf8_bin DEFAULT NULL COMMENT 'Тип документа',
  `document_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  UNIQUE KEY `UK_web_document_rights` (`document_type`,`document_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
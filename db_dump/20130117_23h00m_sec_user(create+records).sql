-- #9 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `sec_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
  `group_id` int(10) unsigned DEFAULT NULL COMMENT 'ИД группы для администрирования и модерирования',
  `login` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT 'Имя пользователя',
  `fullname` varchar(128) COLLATE utf8_bin DEFAULT NULL COMMENT 'ФИО',
  `email` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `about` text COLLATE utf8_bin COMMENT 'О себе',
  `country` VARCHAR( 64 ) NULL DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `district` varchar(48) COLLATE utf8_bin DEFAULT NULL,
  `locality` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `iswoman` tinyint(1) DEFAULT NULL COMMENT 'Пол',
  `bornyear` year(4) DEFAULT NULL,
  `bornmonth` SMALLINT UNSIGNED NULL DEFAULT NULL,
  `bornday` SMALLINT UNSIGNED NULL DEFAULT NULL,
  `phone` VARCHAR( 32 ) NULL DEFAULT NULL,
  `contacts` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `pw` char(32) COLLATE utf8_bin DEFAULT NULL,
  `regdate` datetime DEFAULT NULL COMMENT 'Дата и время регистрации',
  `csid` char(32) COLLATE utf8_bin DEFAULT NULL COMMENT 'Код подтверждения регистрации',
  `isbanned` tinyint(1) DEFAULT NULL COMMENT 'Признак: 1 - забанен',
  `status` tinyint(1) DEFAULT NULL COMMENT 'Статус (confirm.../не действует/действует)',
  `restpwcode` char(32) COLLATE utf8_bin DEFAULT NULL,
  `photo` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `social_network` VARCHAR( 32 ) NULL DEFAULT NULL,
  `social_profile` VARCHAR( 256 ) NULL DEFAULT NULL,
  `lastdate` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Дата и время последнего обращения',
  `quanqr` int(11) NOT NULL DEFAULT '0' COMMENT 'Кол-во запросов к веб-серверу',
  `quanqr_sa` int(11) NOT NULL DEFAULT '0' COMMENT 'Кол-во запросов к серверу-приложений',
  `quan_sessions` int(11) NOT NULL DEFAULT '0' COMMENT 'Кол-во сессий',
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  KEY `group_id` (`group_id`),
  FULLTEXT KEY `about` (`about`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=97 COMMENT='Пользователи' AUTO_INCREMENT=1 ;

INSERT INTO `sec_user` (`id`, `group_id`, `login`, `fullname`, `email`, `about`, `subject_id`, `district`, `locality`, `iswoman`, `bornyear`, `contacts`, `pw`, `regdate`, `csid`, `isbanned`, `status`, `restpwcode`, `photo`, `lastdate`, `quanqr`, `quanqr_sa`, `quan_sessions`) VALUES
(0, 0, 'guest', NULL, '*', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2013-01-01 00:00:01', 0, 0, 0);

UPDATE sec_user SET id = 0 WHERE (id = 1);

INSERT INTO `sec_user` (`id`, `group_id`, `login`, `fullname`, `email`, `about`, `subject_id`, `district`, `locality`, `iswoman`, `bornyear`, `contacts`, `pw`, `regdate`, `csid`, `isbanned`, `status`, `restpwcode`, `photo`, `lastdate`, `quanqr`, `quanqr_sa`, `quan_sessions`) VALUES
(1, 1, 'root', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ca121bae90310c4015975adb4dc62619', '2013-01-01 00:00:00', NULL, NULL, 1, NULL, NULL, '2013-01-01 00:00:00', 0, 0, 0),
(2, 2, 'admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ca121bae90310c4015975adb4dc62619', '2013-01-01 00:00:00', NULL, NULL, 1, NULL, NULL, NULL, 0, 0, 0),
(99, 99, 'test', NULL, '*', NULL, NULL, NULL, NULL, 0, NULL, NULL, '098f6bcd4621d373cade4e832627b4f6', '2013-01-01 00:00:00', NULL, NULL, 1, NULL, NULL, NULL, 0, 0, 0);

ALTER TABLE `sec_user` ORDER BY `id`;
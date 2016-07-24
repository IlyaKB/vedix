-- #3 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `crs_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `firstdate` datetime DEFAULT NULL COMMENT 'Дата первого обращения',
  `lastdate` datetime DEFAULT NULL COMMENT 'Дата последнего обращения',
  `sesquan` int(11) NOT NULL DEFAULT '0',
  `quanqr` int(11) NOT NULL DEFAULT '0' COMMENT 'Кол-во запросов к веб-серверу',
  `quanqr_c` int(11) NOT NULL DEFAULT '0' COMMENT 'Кол. запросов к веб-серверу клиента. TODO: Del',
  `quanqr_sa` int(11) NOT NULL DEFAULT '0' COMMENT 'Кол-во запросов к серверу-приложений',
  `lastsession_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lastsession_id` (`lastsession_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Веб-сайт. IP-адреса' AUTO_INCREMENT=1 ;
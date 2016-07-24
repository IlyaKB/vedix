-- #5 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `crs_session_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
  `sessionid` char(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'ИД сессии',
  `ip_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'ИД пользователя',
  `group_id` int(11) DEFAULT NULL,
  `isbot` tinyint(1) DEFAULT NULL,
  `smgmt` int(11) DEFAULT NULL COMMENT 'Смещение по часовым поясам',
  `stime` datetime DEFAULT NULL COMMENT 'Дата и время первого обращения к страницам сайта',
  `timeout` int(11) DEFAULT NULL,
  `etime` datetime DEFAULT NULL COMMENT 'Дата и время последнего обращения к страницам сайта',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Признак активности сессии',
  `http_referer` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'Web-страница, с которой зашёл пользователь на сайт',
  `agent_id` int(11) DEFAULT NULL COMMENT 'Идентификация браузера пользователя',
  `host_id` int(11) DEFAULT NULL COMMENT 'Под каким хостом работали с сайтом',
  `quanqr` int(11) NOT NULL DEFAULT '0' COMMENT 'Количество обращений к веб-серверу',
  `quanqr_sa` int(11) NOT NULL DEFAULT '0' COMMENT 'Кол-во запросов к серверу-приложений',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ip_id` (`ip_id`),
  KEY `agent_id` (`agent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Сессии - история' AUTO_INCREMENT=1 ;
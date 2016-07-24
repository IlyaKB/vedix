-- #15 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `web_poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(128) COLLATE utf8_bin NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL COMMENT '0/null - да/нет; 1 - один из вариантов; 2 - несколько вариантов',
  `imgurlcode` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '32 символный код для ссылки на график',
  `repeatmode` smallint(6) DEFAULT NULL COMMENT '0/null - можно голосовать сколько угодно раз, 1 - контроль по сессии, 2 - контроль по IP',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;
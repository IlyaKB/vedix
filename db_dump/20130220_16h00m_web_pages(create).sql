-- #13 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `web_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `doctype_id` int(11) DEFAULT NULL,
  `docnumber` smallint(6) DEFAULT NULL,
  `docdate` date DEFAULT NULL,
  `caption` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `urlcaption` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `smallcaption` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT 'Краткое название (для облака тегов)',
  `fullcaption` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT 'Полное название',
  `announce` text COLLATE utf8_bin,
  `announceimg_id` int(11) DEFAULT NULL,
  `upddate` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT 'Признак: 0 - не доступен, 1 - доступен',
  `author_id` int(11) DEFAULT NULL COMMENT 'ИД пользователя',
  `isallowcomments` tinyint(1) DEFAULT '1',
  `ispremoderation` tinyint(1) DEFAULT NULL,
  `hits` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `ahits` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Просмотры анонса',
  `flag` smallint(6) DEFAULT NULL COMMENT 'Опция: 1 - не отображать заголовок и доп.инфу, 2 - заголовок без доп.инфы',
  `body` longtext COLLATE utf8_bin,
  `srcinfo` varchar(128) COLLATE utf8_bin DEFAULT NULL COMMENT 'Источник',
  `isredirection_body` tinyint(1) DEFAULT NULL COMMENT '1 - Включить редиректы на ссылки в тексте документа',
  `isredirection_srcinfo` tinyint(1) DEFAULT NULL COMMENT '1 - Включить редиректы в ссылках на первоисточник',
  `tags` varchar(128) COLLATE utf8_bin DEFAULT NULL COMMENT 'Перечень ключевых слов документа (теги)',
  `metadesc` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `metakeyw` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `isincdesc` tinyint(1) DEFAULT NULL COMMENT 'Дополнять описанием из параметров сайта/раздела',
  `isinckeyw` tinyint(1) DEFAULT NULL COMMENT 'Дополнять ключевыми словами из параметров сайта/раздела',
  `isallowvote` tinyint(1) DEFAULT NULL,
  `vote_count` INT NULL DEFAULT NULL,
  `vote` FLOAT NULL DEFAULT NULL,
  `rightbyif` BOOLEAN NULL DEFAULT NULL COMMENT 'Направление прав',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `doctype_id` (`doctype_id`),
  FULLTEXT KEY `wd_fulltext` (`body`,`announce`,`fullcaption`,`caption`,`metadesc`,`metakeyw`,`smallcaption`,`tags`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Пользовательские страницы' AUTO_INCREMENT=2 ;

INSERT INTO `web_page` (`id`, `category_id`, `doctype_id`, `docnumber`, `docdate`, `caption`, `urlcaption`, `smallcaption`, `fullcaption`, `announce`, `announceimg_id`, `upddate`, `status`, `author_id`, `isallowcomments`, `ispremoderation`, `hits`, `ahits`, `flag`, `body`, `srcinfo`, `isredirection_body`, `isredirection_srcinfo`, `tags`, `metadesc`, `metakeyw`, `isincdesc`, `isinckeyw`, `isallowvote`) VALUES
(1, 0, NULL, 1, '2013-01-01', 'Главная страница', 'mainpage', NULL, NULL, NULL, NULL, '2013-02-16 20:07:22', 1, NULL, NULL, NULL, 0, 0, NULL, '<div>\r\n		<h1 class="f14b">Добро пожаловать на сайт!</h1>\r\n		<div dir="ltr">\r\n			<span style="font-size:small">\r\n				<div style="text-align:right"><i><span style="font-size:x-small">Когда окончится война, единой верой в милосердье, </span></i></div>\r\n				<div style="text-align:right"><i><span style="font-size:x-small">Любовь останется одна для всех религией последней. </span></i></div>\r\n				<div style="text-align:right"><i><span style="font-size:x-small">И век из века без любви, на этой маленькой планете, </span></i></div>\r\n				<div style="text-align:right"><i><span style="font-size:x-small">Мы были вовсе не враги, а просто брошенные дети... </span></i></div>\r\n				<div style="text-align:right"><i><span style="font-size:x-small">С. Трофимов. </span></i></div>\r\n			</span>\r\n		</div></div>', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL);
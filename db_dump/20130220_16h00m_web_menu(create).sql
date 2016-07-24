-- #11 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `web_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `number` smallint(6) DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `code` varchar(24) COLLATE utf8_bin DEFAULT NULL,
  `src` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `description` text COLLATE utf8_bin,
  `itemtype` smallint(6) DEFAULT NULL,
  `isright` tinyint(1) DEFAULT NULL,
  `rightbyif` tinyint(1) DEFAULT NULL,
  `isnotinuse` tinyint(1) NOT NULL DEFAULT '0',
  `isnoindex` tinyint(1) DEFAULT NULL,
  `isnofollow` tinyint(1) DEFAULT NULL,
  `isredirection` tinyint(1) DEFAULT NULL,
  `tag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=47 AUTO_INCREMENT=20 ;

INSERT INTO `web_menu` (`id`, `parent_id`, `number`, `name`, `code`, `src`, `description`, `itemtype`, `isright`, `rightbyif`, `isnotinuse`, `isnoindex`, `isnofollow`, `isredirection`, `tag`) VALUES
(1, NULL, 1, 'Главное меню сайта', 'mainmenu', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(2, 1, 10, 'Главная', 'main', '/', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(3, 1, 20, 'Новости', 'news', '/news/', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(4, 1, 30, 'Интерактивная карта посёлка', 'prpmap', '/prpmap/', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(5, 1, 40, 'Наши фото и видео', 'media', '/media/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL),
(6, 1, 50, 'Интернет-магазин', 'magazin', '/magazin/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL),
(7, 1, 60, 'Форум', 'forum', '/forum/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL),
(8, 1, 70, 'Объявления', 'sitemap', '/sitemap/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL),
(9, 1, 80, 'Контакты и проезд', 'about', '/about/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL),
(10, 1, 90, 'Карта сайта', 'sitemap', '/sitemap/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL),
(11, NULL, 2, 'Левое меню сайта', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(12, 11, 10, 'Доска объявлений', 'adverts', '/adverts/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL),
(13, 11, 20, 'Личные страницы жителей', 'pomestya', '/pomestya/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL),
(14, 11, 40, 'Голосовалки и анкеты', 'questions', '/questions/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL),
(15, 11, 50, 'Вопрос/ответ', 'faq', '/faq/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL),
(16, 11, 60, 'Рассылки', 'subscribes', '/subscribes/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL),
(17, 11, 70, 'Каталог файлов', 'files', '/files/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL),
(18, 11, 80, 'Дружественные сайты', 'sites', '/sites/', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL);
-- #1 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `crs_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `browser_id` int(11) NOT NULL DEFAULT '0' COMMENT 'ИД группы браузеров',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `browser_id` (`browser_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Агенты' AUTO_INCREMENT=1 ;

-- #2 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `crs_host` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Хосты' AUTO_INCREMENT=1 ;

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

-- #4 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `crs_session` (
  `session_id` int(11) NOT NULL COMMENT 'ИД сессии',
  `sessionid` char(32) COLLATE utf8_bin DEFAULT NULL COMMENT 'SESSIONID',
  `ip_id` int(11) NOT NULL,
  `ip` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'ИД пользователя',
  `group_id` int(11) DEFAULT NULL COMMENT 'ИД группы',
  `smgmt` int(11) DEFAULT NULL COMMENT 'Смещение по часовым поясам',
  `stime` datetime DEFAULT NULL COMMENT 'Дата и время первого обращения к сайту',
  `timeout` int(11) DEFAULT NULL COMMENT 'Срок действия сессии в секундах',
  `puttime` datetime DEFAULT NULL COMMENT 'Дата и время последнего обращения к страницам сайта',
  `isshowerror` tinyint(1) DEFAULT '0' COMMENT 'Признак - выводить сообщения об ошибках',
  `user_login` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `user_fullname` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `group_code` varchar(24) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT 'null - удалён, 0 - забанен, 1 - Ok',
  `isbot` tinyint(1) DEFAULT NULL,
  `isbanned` tinyint(1) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  `agentname` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `http_referer` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `quanqr` int(11) NOT NULL DEFAULT '0' COMMENT 'Кол-во запросов к веб-серверу',
  `quanqr_sa` int(11) NOT NULL DEFAULT '0' COMMENT 'Кол-во запросов к серверу-приложений',
  UNIQUE KEY `session_id` (`session_id`),
  KEY `society_id` (`user_id`),
  KEY `group_id` (`group_id`),
  KEY `ip_id` (`ip_id`),
  KEY `sessionid` (`sessionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Текущие сессии';

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

-- #7 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `sas_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `number` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Подсистема SAS. Типы событий.' AUTO_INCREMENT=10 ;

INSERT INTO `sas_event` (`id`, `name`, `number`) VALUES
(1, 'Аутентификация', 1),
(2, 'Отказ в доступе к форме ИС', 2),
(3, 'Действия с записями (редактирование)/ПД', 3),
(5, 'Документооборот', 4),
(6, 'Печать отчётов', 5),
(9, 'Системные', 9),
(10, 'Работа с файлами', 6);

CREATE TABLE IF NOT EXISTS `sas_evtdetail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `number` smallint(6) DEFAULT NULL,
  `name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=35 ;

INSERT INTO `sas_evtdetail` (`id`, `event_id`, `number`, `name`) VALUES
(1, 1, 1, 'Успешный вход'),
(2, 1, 2, 'Не верный логин/пароль'),
(3, 6, 1, 'Простой отчёт (по набору данных)'),
(4, 3, 8, 'Изменение порядкового номера записей'),
(5, 3, 7, 'Fast-модификация'),
(6, 3, 9, 'Создание записи на основе другой (копирование)'),
(7, 3, 1, 'Добавление записи'),
(8, 3, 2, 'Изменение записи'),
(9, 3, 3, 'Перемещение записи'),
(10, 3, 4, 'Копирование записи'),
(11, 3, 5, 'Удаление записи'),
(12, 3, 10, 'Автоматическое создание записи (алгоритмом)'),
(13, 3, 6, 'Выполнение пользовательского действия'),
(14, 2, 1, 'Нет доступа к веб-приложению'),
(15, 2, 2, 'Нет доступа к разделу'),
(16, 2, 3, 'Нет доступа к бизнес-объекту'),
(17, 2, 4, 'Нет доступа к действию'),
(18, 1, 3, 'Выход из системы'),
(19, 1, 4, 'Попытка входа под заблокированной учётной записью'),
(20, 1, 5, 'Попытка входа с приостановленной учётной записью'),
(21, 9, 1, 'Ошибка в SQL-запросе'),
(22, 10, 1, 'Добавление файла'),
(23, 10, 2, 'Удаление файла'),
(24, 10, 5, 'Скачивание защищённого файла'),
(25, 10, 3, 'Удаление не сущ.файла из системы'),
(26, 10, 4, 'Изменение прикреплённого файла'),
(27, 3, 11, 'Автоматическое изменение записи (алгоритмом)'),
(28, 3, 12, 'Автоматическое удаление записи (алгоритмом)'),
(29, 10, 6, 'Автом.удаление файла из системы'),
(30, 10, 7, 'Автом.удаление не сущ.файла'),
(31, 3, 13, 'Прикрепление файла к записи (загрузка)'),
(32, 3, 14, 'Редактирование прикреплённого файла (информ.)'),
(33, 3, 15, 'Удаление прикреплённого к записи файла'),
(34, 3, 16, 'Скачивание прикреплённого к записи файла');

-- #8 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `sec_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `number` smallint(6) DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `code` varchar(24) COLLATE utf8_bin NOT NULL,
  `isnotinuse` tinyint(1) DEFAULT NULL,
  `isshowerror` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

INSERT INTO `sec_group` (`id`, `parent_id`, `number`, `name`, `code`, `isnotinuse`, `isshowerror`) VALUES
(1, NULL, NULL, 'Root', 'root', NULL, 1),
(0, 1, 3, 'Guests', 'guests', NULL, NULL);

UPDATE sec_group SET id = 0 WHERE (id = 2);

INSERT INTO `sec_group` (`id`, `parent_id`, `number`, `name`, `code`, `isnotinuse`, `isshowerror`) VALUES
(2, 1, 1, 'Administrators', 'admins', NULL, 1),
(3, 1, 2, 'Authorizen users', 'users', NULL, NULL),
(99, 1, 13, 'Testers', 'testers', NULL, 1);

ALTER TABLE `sec_group` ORDER BY `id`;

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

-- #10 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `sas_redirection_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) DEFAULT NULL,
  `godate` datetime DEFAULT NULL,
  `srcpage` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `destlink` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='История переадресаций (переходов на другие сайты)' AUTO_INCREMENT=1 ;

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

-- #12 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `web_news` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=2365 COMMENT='Новости' AUTO_INCREMENT=2 ;

INSERT INTO `web_news` (`id`, `category_id`, `doctype_id`, `docnumber`, `docdate`, `caption`, `urlcaption`, `smallcaption`, `fullcaption`, `announce`, `announceimg_id`, `upddate`, `status`, `author_id`, `isallowcomments`, `ispremoderation`, `hits`, `ahits`, `flag`, `body`, `srcinfo`, `isredirection_body`, `isredirection_srcinfo`, `tags`, `metadesc`, `metakeyw`, `isincdesc`, `isinckeyw`, `isallowvote`) VALUES
(1, 0, NULL, 1, '2013-11-11', 'Первая новость', 'pervaya_novost', 'Это первая новость', 'Это первая новость', 'Это первая новость. Далее идёт первой новости... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст...', NULL, '2013-11-11 12:00:00', 1, 2, 1, NULL, 1, 1, NULL, 'Это первая новость.\r\n Далее идёт первой новости...\r\nТекст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст... Текст...', 'Администрация сайта', 1, 1, NULL, NULL, NULL, NULL, NULL, 1);

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

-- #16 2013.11.11 12:00:00

CREATE TABLE IF NOT EXISTS `web_poll_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `number` smallint(6) DEFAULT NULL,
  `text` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `votes` INT(6) NOT NULL DEFAULT '0' COMMENT 'Количество отметок',
  `color` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- #17 2013.11.11 12:15:00

CREATE TABLE IF NOT EXISTS `web_document_rights` (
  `document_type` enum('page','news','state','blog') COLLATE utf8_bin DEFAULT NULL COMMENT 'Тип документа',
  `document_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  UNIQUE KEY `UK_web_document_rights` (`document_type`,`document_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- #18 2014.04.13 21:30:00

ALTER TABLE `web_menu` ADD INDEX(`code`);

-- #19 2014.04.16 16:00:00

ALTER TABLE `web_page` ADD UNIQUE(`urlcaption`);

-- #20 2014.06.10 12:30:00

ALTER TABLE `web_menu` ADD `object_type` VARCHAR(16) NULL DEFAULT NULL AFTER `description`, ADD `object_id` INT NULL DEFAULT NULL AFTER `object_type`, ADD INDEX (`object_type`, `object_id`) ;

-- #21 2014.06.13 11:30:00

ALTER TABLE `sec_user` ADD UNIQUE `socnetlogin` (`social_network`,`login`);

-- #22 2014.06.14 11:00:00

CREATE TABLE IF NOT EXISTS `dbr_bobject` (
  `cid` int(11) NOT NULL COMMENT 'ИД позиции в каталоге веб-решений',
  `catalog_id` int(11) DEFAULT NULL,
  `alg_before_add` text COLLATE utf8_bin COMMENT 'Триггер перед добавлением',
  `alg_after_edit` text COLLATE utf8_bin COMMENT 'Тригер после изменения',
  `alg_before_del` text COLLATE utf8_bin COMMENT 'Триггер после удаления',
  `alg_before_add_upddate` datetime DEFAULT NULL,
  `alg_after_edit_upddate` datetime DEFAULT NULL,
  `alg_before_del_upddate` datetime DEFAULT NULL,
  `tablename` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT 'Имя таблицы бизнес-объекта',
  `sas_fields` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `attachments` tinyint(1) DEFAULT NULL COMMENT 'Признак - Вкл.прикрепление файлов',
  `attachmentsfk` varchar(24) COLLATE utf8_bin DEFAULT NULL COMMENT 'Код СД',
  `attachments_f_tbl` varchar(24) COLLATE utf8_bin DEFAULT NULL COMMENT 'Имя таблицы папок записей',
  `attachments_r_tbl` varchar(24) COLLATE utf8_bin DEFAULT NULL COMMENT 'Имя таблицы прав доступа папок прикр.файлов',
  `attachments_r_fld` varchar(24) COLLATE utf8_bin DEFAULT NULL COMMENT 'Поле таблицы прав доступа',
  `folderfiles` varchar(64) COLLATE utf8_bin DEFAULT NULL COMMENT 'Путь к размещению файлов (свободный доступ)',
  `deffilesharing` tinyint(1) DEFAULT NULL COMMENT 'Доступ к файлам по умолчанию (1 - закрытый)',
  `act_show` tinyint(1) NOT NULL DEFAULT '0',
  `act_add` tinyint(1) NOT NULL DEFAULT '0',
  `act_edit` tinyint(1) NOT NULL DEFAULT '0',
  `act_move` tinyint(1) NOT NULL DEFAULT '0',
  `act_copy` tinyint(1) NOT NULL DEFAULT '0',
  `act_delete` tinyint(1) NOT NULL DEFAULT '0',
  `act_attachments` tinyint(1) NOT NULL DEFAULT '0',
  `act_show_rdir` tinyint(1) DEFAULT NULL COMMENT 'Направление прав: 0 - обратное, 1- прямое, null - не исп.',
  `act_add_rdir` tinyint(1) DEFAULT NULL,
  `act_edit_rdir` tinyint(1) DEFAULT NULL,
  `act_move_rdir` tinyint(1) DEFAULT NULL,
  `act_copy_rdir` tinyint(1) DEFAULT NULL,
  `act_delete_rdir` tinyint(1) DEFAULT NULL,
  `act_attachments_rdir` tinyint(1) DEFAULT NULL,
  `components_link` varchar(128) COLLATE utf8_bin DEFAULT NULL COMMENT 'Перечень ИД веб-компонентов',
  `sqlscript` text COLLATE utf8_bin,
  UNIQUE KEY `catalog_id` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=54;

INSERT INTO `dbr_bobject` (`cid`, `catalog_id`, `alg_before_add`, `alg_after_edit`, `alg_before_del`, `alg_before_add_upddate`, `alg_after_edit_upddate`, `alg_before_del_upddate`, `tablename`, `sas_fields`, `attachments`, `attachmentsfk`, `attachments_f_tbl`, `attachments_r_tbl`, `attachments_r_fld`, `folderfiles`, `deffilesharing`, `act_show`, `act_add`, `act_edit`, `act_move`, `act_copy`, `act_delete`, `act_attachments`, `act_show_rdir`, `act_add_rdir`, `act_edit_rdir`, `act_move_rdir`, `act_copy_rdir`, `act_delete_rdir`, `act_attachments_rdir`, `components_link`, `sqlscript`) VALUES
(32, 32, NULL, NULL, NULL, NULL, NULL, NULL, 'dbr_catalog', 'code,name', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, NULL, NULL, NULL),
(33, 33, NULL, NULL, NULL, NULL, NULL, NULL, 'sec_user', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, NULL, NULL, NULL);

-- #23 2014.09.22 18:30:00

ALTER TABLE `web_menu` ADD `isdemo` BOOLEAN NULL DEFAULT NULL AFTER `isredirection`;

-- #24 2014.10.02 23:00:00

CREATE TABLE IF NOT EXISTS `web_faq_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(24) DEFAULT NULL,
  `number` smallint(6) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `number` (`number`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Site - FAQ - Categories' AUTO_INCREMENT=6 ;

INSERT INTO `web_faq_category` (`id`, `code`, `number`, `name`, `status`) VALUES
(1, 'other', 999, 'Прочее', 1),
(2, 'common', 1, 'Общие вопросы', 1),
(3, 'technical', 2, 'Технические вопросы', 1),
(4, 'cms', 3, 'Панель управления', 1),
(5, 'finance', 4, 'Финансовые и юридические вопросы', 0);

CREATE TABLE IF NOT EXISTS `web_faq_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `number` smallint(6) DEFAULT NULL,
  `question` text,
  `reply` text,
  `counter_like` int(11) NOT NULL DEFAULT '0',
  `counter_dislike` int(11) NOT NULL DEFAULT '0',
  `creation_date` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  FULLTEXT KEY `question` (`question`,`reply`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Site - FAQ - Questions' AUTO_INCREMENT=15 ;

INSERT INTO `web_faq_question` (`id`, `category_id`, `number`, `question`, `reply`, `counter_like`, `counter_dislike`, `creation_date`, `status`) VALUES
(1, 1, 1, 'Текст вопроса...', 'Ответ...', 10, 3, '2014-10-02 23:07:45', 1),
(2, 1, 2, 'Текст вопроса...', 'Ответ...', 3, 10, '2014-10-03 09:33:18', 1),
(3, 1, 3, 'Текст вопроса...', 'Ответ...', 1, 2, '2014-10-03 09:33:28', 1),
(4, 2, 1, 'Какие доменные зоны поддерживаются на хостинге?', 'Любые', 23, 8, '2014-10-03 09:33:36', 1),
(5, 2, 2, 'Как перенести сайт с другого хостинга?', 'Можно обратится к нам с помощью формы обратной связи, либо пройти регистрацию на сайте. Заказать тарифный план с переносом существующего домена...', 11, 12, '2014-10-03 09:33:50', 1),
(6, 3, 1, 'Какая версия PHP используется на хостинге?', 'На всех тарифах Linux используется последняя стабильная версия: PHP 5.4.13', 4, 3, '2014-10-03 09:34:02', 1),
(7, 3, 1, 'Какие базы данных можно использовать?', 'На Linux хостинге Вы можете использовать MySQL 5.5', 20, 11, '2014-10-03 09:34:13', 1),
(8, 4, 2, 'Как зайти в панель управления?', 'Воспользоваться ссылкой Панель управления', 1, 2, '2014-10-03 09:34:28', 1),
(9, 5, 3, 'Какие данные мне необходимо предоставить для регистрации?', 'От физических лиц требуется:<br/>\r\n - контактный адрес электронной почты,<br/>\r\n - телефон,<br/>\r\n - паспортные данные.<br/>\r\n <br/>\r\n От юридических лиц требуется:<br/>\r\n - учетная карточка организации,<br/>\r\n - контактный телефон,<br/>\r\n - данные контактной персоны от организации.', 1, 0, '2014-10-03 09:34:41', 0);

-- #25 2014.10.11 16:30:00

ALTER TABLE `web_page` CHANGE `vote_count` `votes_count` INT(11) NULL DEFAULT NULL, CHANGE `vote` `votes` FLOAT NULL DEFAULT NULL;

-- #26 2014.10.18 22:00:00

RENAME TABLE `web_page` TO `web_pages`;

-- #27 2014.10.27 09:30:00

CREATE TABLE IF NOT EXISTS `web_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_type` enum('page','news','state','post') DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `author_name` varchar(64) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `text` text,
  `counter_like` int(11) NOT NULL DEFAULT '0',
  `counter_dislike` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) DEFAULT NULL COMMENT 'NULL - требует модерации, 1 - ok, 0 - скрыт',
  PRIMARY KEY (`id`),
  KEY `entity_type` (`entity_type`,`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- #28 2015.01.06 20:00:00

CREATE TABLE IF NOT EXISTS `tra_stocks` (
  `id` int(11) NOT NULL,
  `code` char(4) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Trading - Ценные бумаги (акции)' AUTO_INCREMENT=3 ;

INSERT INTO `tra_stocks` (`id`, `code`, `name`) VALUES
(1, 'SBER', 'Сбербанк'),
(2, 'VTBR', 'ВТБ');

CREATE TABLE IF NOT EXISTS `tra_stock_rates` (
  `id` int(11) NOT NULL,
  `stock_id` int(11) DEFAULT NULL,
  `period` char(3) DEFAULT NULL,
  `ratedate` date DEFAULT NULL,
  `ratetime` time DEFAULT NULL,
  `rateopen` float DEFAULT NULL,
  `ratehigh` float DEFAULT NULL,
  `ratelow` float DEFAULT NULL,
  `rateclose` float DEFAULT NULL,
  `ratemedian` float DEFAULT NULL,
  `volume` int(11) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Trading - Котировки на ценные бумаги' AUTO_INCREMENT=1 ;

ALTER TABLE `tra_stocks` ADD PRIMARY KEY (`id`), ADD KEY `code` (`code`);

ALTER TABLE `tra_stock_rates`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `stock_id` (`stock_id`,`period`,`ratedate`,`ratetime`);

ALTER TABLE `tra_stocks` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;

ALTER TABLE `tra_stock_rates` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

-- #29 2015.03.06 13:40:00

ALTER TABLE `tra_stock_rates` ADD `ticker` CHAR(5) NULL DEFAULT NULL AFTER `stock_id`;

-- #30 2015.04.05 09:30:00

ALTER TABLE `web_news` CHANGE `vote_count` `votes_count` INT(11) NULL DEFAULT NULL, CHANGE `vote` `votes` FLOAT NULL DEFAULT NULL;

-- #31 2015.04.07 09:45:00

ALTER TABLE `web_news` DROP `doctype_id`, DROP `docnumber`, DROP `smallcaption`, DROP `fullcaption`, DROP `flag`, DROP `isincdesc`, DROP `isinckeyw`;

-- #32 2015.04.09 07:45:00

CREATE TABLE IF NOT EXISTS `web_blog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url_name` varchar(32) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `web_blog_categories` (`id`, `url_name`, `name`) VALUES
(1, 'category1', 'Категория первая'),
(2, 'category2', 'Категория вторая');

CREATE TABLE IF NOT EXISTS `web_blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `docdate` date DEFAULT NULL,
  `caption` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `url_name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `smallcaption` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT 'Краткое название (для облака тегов)',
  `announce` text COLLATE utf8_bin,
  `announceimg_id` int(11) DEFAULT NULL,
  `upddate` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT 'Признак: 0 - не доступен, 1 - доступен',
  `author_id` int(11) DEFAULT NULL COMMENT 'ИД пользователя',
  `isallowcomments` tinyint(1) DEFAULT '1',
  `ispremoderation` tinyint(1) DEFAULT NULL,
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `ahits` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Просмотры анонса',
  `body` longtext COLLATE utf8_bin,
  `srcinfo` varchar(128) COLLATE utf8_bin DEFAULT NULL COMMENT 'Источник',
  `isredirection_body` tinyint(1) DEFAULT NULL COMMENT '1 - Включить редиректы на ссылки в тексте документа',
  `isredirection_srcinfo` tinyint(1) DEFAULT NULL COMMENT '1 - Включить редиректы в ссылках на первоисточник',
  `tags` varchar(128) COLLATE utf8_bin DEFAULT NULL COMMENT 'Перечень ключевых слов документа (теги)',
  `metadesc` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `metakeyw` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `isallowvote` tinyint(1) DEFAULT NULL,
  `votes_count` int(11) DEFAULT NULL,
  `votes` float DEFAULT NULL,
  `rightbyif` tinyint(1) DEFAULT NULL COMMENT 'Направление прав',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  FULLTEXT KEY `wd_fulltext` (`body`,`announce`,`caption`,`metadesc`,`metakeyw`,`smallcaption`,`tags`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=2365 COMMENT='Новости' AUTO_INCREMENT=15 ;

ALTER TABLE `web_news` CHANGE `urlcaption` `url_name` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

-- #33 2015.04.11 10:45:00

ALTER TABLE `web_pages` DROP `category_id`, DROP `doctype_id`, DROP `docnumber`, DROP `smallcaption`, DROP `fullcaption`, DROP `announce`, DROP `announceimg_id`, DROP `isincdesc`, DROP `isinckeyw`;
ALTER TABLE `web_pages` CHANGE `urlcaption` `url_name` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

-- #34 2015.04.12 18:30:00

ALTER TABLE `web_comments` CHANGE `entity_type` `entity_type` ENUM('page','news','state','blog') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

-- #35 2015.04.13 07:00:00

ALTER TABLE `web_votes` CHANGE `object_type` `object_type` ENUM('page','news','blog','state','poll') CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;
ALTER TABLE `web_poll` DROP `imgurlcode`;

-- #36 2015.04.17 09:00:00

ALTER TABLE `web_votes` ADD `sessionid` CHAR(32) NULL DEFAULT NULL AFTER `session_id`, ADD `ip` VARCHAR(64) NULL DEFAULT NULL AFTER `sessionid`;

-- #37 2015.04.18 10:45:00

ALTER TABLE `web_menu` ADD `regexp_current` VARCHAR(64) NULL DEFAULT NULL AFTER `tag`;

-- #38 2015.05.24 23:00:00

CREATE TABLE IF NOT EXISTS `mag_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'ИД родительской категории',
  `number` smallint(6) DEFAULT NULL COMMENT 'Номер',
  `url_name` varchar(32) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL COMMENT 'Название категории',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=30 COMMENT='Magazin. Категории' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mag_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_type` enum('magposition') DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `author_name` varchar(64) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `text` text,
  `counter_like` int(11) NOT NULL DEFAULT '0',
  `counter_dislike` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) DEFAULT NULL COMMENT 'NULL - требует модерации, 1 - ok, 0 - скрыт',
  PRIMARY KEY (`id`),
  KEY `entity_type` (`entity_type`,`entity_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=79 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mag_composition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position_id` int(11) NOT NULL,
  `element_id` int(11) DEFAULT NULL COMMENT 'ИД ингредиента',
  `name` varchar(128) DEFAULT NULL COMMENT 'Название',
  `quantity` float DEFAULT NULL COMMENT 'Количество',
  `measure_id` int(11) DEFAULT NULL,
  `costprice` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element_id` (`element_id`),
  KEY `recept_id` (`position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=44 COMMENT='Magazin. Состав' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mag_galleries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position_id` int(11) NOT NULL,
  `photo_url` varchar(128) DEFAULT NULL,
  `description` text,
  `name_alt` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `position_id` (`position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Magazin. Фотографии галери товара/услуги' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mag_manufacturers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Magazin. Изготовители' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mag_measures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_short` varchar(10) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=24 COMMENT='Magazin. Единицы измерения' AUTO_INCREMENT=13 ;

INSERT INTO `mag_measures` (`id`, `name_short`, `name`) VALUES
(1, 'г', 'граммы'),
(2, 'мл', 'миллилитры'),
(3, 'шт', 'штуки'),
(4, 'стол.лож.', 'столовая ложка'),
(5, 'чайн.лож.', 'чайная ложка'),
(6, 'стакан(ов)', 'стакан'),
(7, 'щепотка(и)', 'щепотка'),
(8, 'кг', 'килограммы'),
(9, 'л', 'литры'),
(10, 'ч', 'часы'),
(11, 'кВт', 'Киловатты'),
(12, 'горсть(ей)', 'Горсть');

CREATE TABLE IF NOT EXISTS `mag_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL COMMENT 'ИД категории',
  `number` smallint(6) DEFAULT NULL COMMENT 'Порядковый номер рецепта в пределах категории для сортировки',
  `name` varchar(128) DEFAULT NULL COMMENT 'Название рецепта (блюда)',
  `description` text COMMENT 'Описание приготовления',
  `status` tinyint(1) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT '1',
  `url_name` varchar(128) DEFAULT NULL,
  `dateplacement` datetime DEFAULT NULL,
  `photo_id_` int(11) DEFAULT NULL COMMENT 'ИД файла в системе с фотографией',
  `photo_url` varchar(255) DEFAULT NULL,
  `manufacturer_id` int(11) DEFAULT NULL COMMENT 'ИД изготовителя',
  `quantity` float DEFAULT NULL COMMENT 'Количество (для заказов)',
  `measure_id` int(11) DEFAULT NULL,
  `costprice` float DEFAULT NULL COMMENT 'Себестоимость',
  `price` float DEFAULT NULL COMMENT 'Цена (если проставлена, то для рецепта доступна форма заказа)',
  `is_alloworder` tinyint(1) DEFAULT NULL,
  `hits` int(11) DEFAULT NULL COMMENT 'Просмотры',
  `isallowcomments` tinyint(1) DEFAULT NULL COMMENT 'Разрешить комментарии',
  `ispremoderation` tinyint(1) DEFAULT NULL COMMENT 'Включить премодерацию',
  `isallowvote` tinyint(1) DEFAULT NULL,
  `votes_count` int(11) DEFAULT NULL,
  `votes` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `author_id` (`manufacturer_id`),
  KEY `category_id` (`category_id`),
  KEY `photo_id` (`photo_id_`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1014 COMMENT='Magazin. Товары и услуги' AUTO_INCREMENT=1;

-- #39 2015.06.08 12:30:00

ALTER TABLE `mag_manufacturers` ADD `url_name` VARCHAR(32) NULL DEFAULT NULL AFTER `user_id`, ADD UNIQUE (`url_name`) ;

-- #40 2016.07.24 14:30:00

CREATE TABLE IF NOT EXISTS `ett_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` char(20) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `createdate` date DEFAULT NULL,
  `customer` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='ETTON. Список заказов' AUTO_INCREMENT=60 ;

INSERT INTO `ett_orders` (`id`, `number`, `user_id`, `createdate`, `customer`) VALUES
(1, 'A00000000123860', 99, '2016-07-20', 'АААА'),
(2, 'A00000000123887', 99, '2016-07-21', 'АААА'),
(50, 'Y0001', 0, '2016-07-24', 'YYY'),
(49, 'H00001', 0, '2016-07-24', 'HHHH'),
(59, 'qwer', 0, '2016-07-24', 'qwer');

CREATE TABLE IF NOT EXISTS `ett_orders_spec` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `position_id` int(11) unsigned NOT NULL,
  `subtype_id` int(11) DEFAULT NULL,
  `quantity` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_position_subtype` (`order_id`,`position_id`,`subtype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='ETTON. Спецификация заказа' AUTO_INCREMENT=45 ;

INSERT INTO `ett_orders_spec` (`id`, `order_id`, `position_id`, `subtype_id`, `quantity`) VALUES
(1, 1, 1, 1, 6),
(2, 1, 1, 2, 5),
(3, 1, 2, 1, 1),
(4, 1, 3, NULL, 1),
(5, 2, 3, NULL, 10),
(41, 59, 1, 1, 2),
(40, 49, 3, NULL, 5),
(39, 50, 1, 1, 5),
(44, 59, 2, 3, 8),
(33, 49, 2, 3, 2),
(32, 50, 3, NULL, 100),
(43, 59, 3, NULL, 5);

CREATE TABLE IF NOT EXISTS `ett_positions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='ETTON. Каталог товаров' AUTO_INCREMENT=4 ;

INSERT INTO `ett_positions` (`id`, `name`) VALUES
(1, 'Рабочая тетрадь'),
(2, 'Ручка'),
(3, 'Карандаш');

CREATE TABLE IF NOT EXISTS `ett_positions_subtypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `position_id` int(10) unsigned NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `position_id` (`position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='ETTON. Субтипы товаров' AUTO_INCREMENT=5 ;

INSERT INTO `ett_positions_subtypes` (`id`, `position_id`, `name`, `is_default`) VALUES
(1, 1, 'В клеточку', 1),
(2, 1, 'В строчку', NULL),
(3, 2, 'С пастой синего цвета', 1),
(4, 2, 'С пастой красного цвета', NULL);

-- #41 2016.08.01 19:00:00

TRUNCATE web_menu;
INSERT INTO `web_menu` (`id`, `parent_id`, `number`, `name`, `code`, `src`, `description`, `object_type`, `object_id`, `itemtype`, `isright`, `rightbyif`, `isnotinuse`, `isnoindex`, `isnofollow`, `isredirection`, `isdemo`, `tag`, `regexp_current`) VALUES
(1, NULL, 1, 'Главное меню сайта', 'mainmenu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 10, 'Главная', 'main', '/', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, '/^\\/$|^\\/index\\.php$/i'),
(3, 1, 20, 'Новости', 'code20', '/news', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '/^\\/news$|^\\/news\\/.*/i'),
(4, 1, 30, 'Карта', 'code30', '/map/', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL),
(24, 6, 1, 'Продукты питания', 'code1', '/magazin/producti_pitaniya', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, NULL, NULL),
(5, 1, 40, 'Наши фото и видео', 'media', '/media/', NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 1, NULL, NULL),
(6, 1, 50, 'Интернет-магазин', 'code50', '/magazin', NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, '/^\\/magazin$|^\\/magazin\\/.*/i'),
(7, 1, 60, 'Форум', 'forum', '/forum/', NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 1, NULL, NULL),
(9, 1, 80, 'Контакты и проезд', 'code80', '/about/', NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, 1, NULL, NULL),
(11, NULL, 2, 'Левое меню сайта', 'sidemenu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 11, 10, 'Доска объявлений', 'adverts', '/adverts/', NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 1, NULL, NULL),
(13, 11, 20, 'Личные страницы', 'pomestya', '/pomestya/', NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 1, NULL, NULL),
(14, 11, 40, 'Голосовалки и анкеты', 'questions', '/questions/', NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 1, NULL, NULL),
(15, 11, 50, 'Вопрос/ответ', 'faq', '/faq', NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 11, 60, 'Рассылки', 'subscribes', '/subscribes/', NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 1, NULL, NULL),
(17, 11, 70, 'Каталог файлов', 'files', '/files/', NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 1, NULL, NULL),
(18, 11, 80, 'Дружественные сайты', 'sites', '/sites/', NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 1, NULL, NULL),
(20, 7, 10, 'Раздел форума 1', 'forum1', '/forum/section/ggg1', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL),
(21, 7, 20, 'Раздел форума 2', 'forum2', '/forum/section/ggg2', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL),
(22, 7, 30, 'Раздел форума 3', 'forum3', '/forum/section/ggg3', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL),
(23, 20, 1, 'Тема 1 раздела 1', 'theme1', '/forum/theme/hhh1', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL),
(25, 6, 2, 'Мебель', 'code2', '/magazin/mebel', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, NULL, NULL),
(26, 6, 3, 'Услуги', 'code3', '/magazin/services', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, NULL, NULL),
(27, 20, 2, 'Тема 2 раздела 1', 'theme2', '/forum/theme/hhh2', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL),
(28, 27, 1, 'Важное сообщение в теме 2 раздела 1', 'post1', '/forum/theme/hhh2#post1', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL),
(29, 7, 40, NULL, 'divider', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL),
(30, 7, 50, 'Поиск по форуму', 'forum_search', '/forum/search', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL),
(31, 17, 20, 'Документы', 'files_documents', '/files/document', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL),
(32, 17, 10, 'Видео', 'files_video', '/files/video', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL),
(33, 17, 30, 'Фотографии', 'files_photo', '/files/photo', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL),
(34, 1, 107, 'О нас', 'code105', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL),
(47, 1, 105, 'Ещё', 'code106', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL),
(51, 47, 1, 'Опросы', 'code3', '/static/polls', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL),
(44, 1, 101, 'Блог', 'code105', '/blog', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '/^\\/blog[\\/]?.*/i'),
(45, 34, 1, 'Вопросы и ответы', 'code1', '/faq', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '/^\\/faq[\\/]?$/i'),
(46, 34, 2, 'Контакты', 'code2', '/pages/about', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '/\\/pages\\/about/i'),
(48, 47, 2, 'Геополитика', 'code1', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL),
(52, 48, 1, 'Краткий обзор того, что сделал Путин с 2000 по 2014 год', 'code1', '/pages/kratkiy_obzor_togo_chto_sdelal_putin_s_2000_po_2014_god', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL),
(53, 48, 2, 'Смысл войны между Новороссией и Украиной', 'code2', '/pages/smisl_voyni_mezhdu_novorossiey_i_ukrainoy', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL),
(55, 47, 4, 'Тестовое задание Etton', 'code4', '/etton', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL),
(56, 47, 5, 'Тестовое задание Cognitive', 'code5', '/cognitive', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL);

-- #42 2016.08.10 11:30:00

TRUNCATE web_pages;
INSERT INTO `web_pages` (`id`, `docdate`, `caption`, `url_name`, `upddate`, `status`, `author_id`, `isallowcomments`, `ispremoderation`, `hits`, `ahits`, `flag`, `body`, `srcinfo`, `isredirection_body`, `isredirection_srcinfo`, `tags`, `metadesc`, `metakeyw`, `isallowvote`, `votes_count`, `votes`, `rightbyif`) VALUES
(1, '2013-01-01', 'Главная страница', 'index', '2016-08-01 22:14:31', 1, NULL, NULL, NULL, 3, 0, NULL, '<div><br></div><div>Мы — профессиональная команда разработчиков. Сделаем коммерческий&nbsp;сайт, веб-приложение, сервис&nbsp;или&nbsp;информационную&nbsp;систему&nbsp;с веб-интерфейсом.&nbsp;В команду разработчиков входят Front-end/Back-end разработчики, веб-дизайнеры,&nbsp;верстальщики, проектировщики баз данных.</div><div><strong><br></strong></div><div><strong>Организационные вопросы</strong></div><div><strong><br></strong></div><div style="text-align:left">* Работаем только над большими проектами со сроком реализации&nbsp;от 3 месяцев и более.<br>* Работаем по договору и строго по техническому заданию (ТЗ).<br>* Работаем как правило только в будни (5 дней в неделю) с 10:00 до 19:00 по моск. времени (8 часовой рабочий день).<br>* По желанию Заказчика каждый день в определённое время могут проводиться оперативки (Skype), на которых Заказчик может участвовать в обсуждении текущих задач.<br>* Исполнитель на основании требований Заказчика к проекту сообщает примерные&nbsp;сроки и стоимость проекта (даётся грубая оценка).<br>*&nbsp;Реализация проекта выполняется итерациями&nbsp;в 1–4 недели. В конце каждой итерации организуется т.н. демка (сдача итерации), на которой Заказчик даёт оценку выполненной работе и замечания, которые могут быть учтены сразу, если на их устранение требуется не более одного дня.&nbsp;Доработки, непредусмотренные ТЗ и требующие более одного дня, реализовываются только после заключения дополнительного соглашения к договору или с согласия Исполнителя&nbsp;в следующей итерации.</div><div style="text-align:left">*&nbsp;Вы можете предложить свой вариант работы.<br></div><div><br></div><div><strong>Каждая итерация состоит из следующих этапов:</strong></div><div><strong><br></strong></div><div style="text-align:left"><strong>Этап 1.&nbsp;</strong>Формирование Исполнителем/Заказчиком ТЗ с&nbsp;описанием функциональности (бизнес-процессы, модель данных) и&nbsp;схематическими эскизами страниц/форм (представление).&nbsp;Согласование ТЗ между Исполнителем и Заказчиком по содержимому, срокам и стоимости (даётся реальная оценка с возможной коррекцией на 2 этапе).<br><strong>Этап 2.</strong>&nbsp;Разработка дизайна страниц и форм (как будет выглядеть в браузере).&nbsp;Утверждение нарисованных макетов страниц и форм, а также функциональности Заказчиком.<br><strong>Этап 3.</strong>&nbsp;Верстка страниц и форм, программирование серверной и клиентской части, разработка базы данных, тестирование.<br><strong>Этап 4.</strong>&nbsp;Сдача итерации Заказчику на тестовом сервере (демо/обучение).<br><strong>Этап 5.</strong>&nbsp;Разворачивание готовой части продукта на рабочем сервере по требованию Заказчика.</div><div><br></div>', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, '2014-01-01', 'О нас', 'about', '2016-08-01 22:23:16', 1, NULL, NULL, NULL, 41, 0, 1, '<div style="text-align:center"><strong>О нас</strong></div><div style="text-align:center"><strong><br></strong></div><div style="text-align:justify">Мы — разработчики нативного иерархического MVC-движка "VediX", с помощью которого можно разработать веб-приложения, веб-сайт или информационную систему с веб-интерфейсом. Движок использует некоторые готовые решения, например, модифицированный шаблонизатор Mustache и WYSIWYG-редактор elRTE+elFinder. Back-end часть использует PHP5 в качестве серверного языка программирования и СУБД MySQL для работы с базой данных. Front-end часть обычно использует TypeScript как браузерный язык программирования, который компилируется в Javascript на стадии разработки, а также всем известные технологии HTML5, CSS3/LESS. Также используются популярные Javascript-библиотеки, например, jQuery, Bootstrap, KnockoutJS (MVVM) и др.</div><div><br></div><div>Наши контакты:</div><div><br></div><div>Наименование: Группа разработчиков "VediX System"</div><div>Юридический адрес:&nbsp;Россия, 603022, г. Нижний Новгород, ул. Студенческая, д.8 (офис ООО "Программ-Эксперт").</div><div>Телефон: +7 (910)&nbsp;128-15-07</div>', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
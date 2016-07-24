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
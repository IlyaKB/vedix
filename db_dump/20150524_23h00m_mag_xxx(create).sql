-- #38 2015.05.24 23:00:00


--
-- Структура таблицы `mag_categories`
--

CREATE TABLE IF NOT EXISTS `mag_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'ИД родительской категории',
  `number` smallint(6) DEFAULT NULL COMMENT 'Номер',
  `url_name` varchar(32) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL COMMENT 'Название категории',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=30 COMMENT='Magazin. Категории' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mag_comments`
--

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

-- --------------------------------------------------------

--
-- Структура таблицы `mag_composition`
--

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

-- --------------------------------------------------------

--
-- Структура таблицы `mag_galleries`
--

CREATE TABLE IF NOT EXISTS `mag_galleries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position_id` int(11) NOT NULL,
  `photo_url` varchar(128) DEFAULT NULL,
  `description` text,
  `name_alt` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `position_id` (`position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Magazin. Фотографии галери товара/услуги' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mag_manufacturers`
--

CREATE TABLE IF NOT EXISTS `mag_manufacturers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Magazin. Изготовители' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mag_measures`
--

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

--
-- Структура таблицы `mag_positions`
--

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
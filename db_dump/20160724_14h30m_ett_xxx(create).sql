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
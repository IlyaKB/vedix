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
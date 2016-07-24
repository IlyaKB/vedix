-- #32 2015.04.09 07:45:00

CREATE TABLE IF NOT EXISTS `web_blog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url_name` varchar(32) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `web_blog_categories` (`id`, `url_name`, `name`) VALUES
(1, 'category1', '��������� ������'),
(2, 'category2', '��������� ������');

CREATE TABLE IF NOT EXISTS `web_blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `docdate` date DEFAULT NULL,
  `caption` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `url_name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `smallcaption` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '������� �������� (��� ������ �����)',
  `announce` text COLLATE utf8_bin,
  `announceimg_id` int(11) DEFAULT NULL,
  `upddate` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '�������: 0 - �� ��������, 1 - ��������',
  `author_id` int(11) DEFAULT NULL COMMENT '�� ������������',
  `isallowcomments` tinyint(1) DEFAULT '1',
  `ispremoderation` tinyint(1) DEFAULT NULL,
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `ahits` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '��������� ������',
  `body` longtext COLLATE utf8_bin,
  `srcinfo` varchar(128) COLLATE utf8_bin DEFAULT NULL COMMENT '��������',
  `isredirection_body` tinyint(1) DEFAULT NULL COMMENT '1 - �������� ��������� �� ������ � ������ ���������',
  `isredirection_srcinfo` tinyint(1) DEFAULT NULL COMMENT '1 - �������� ��������� � ������� �� �������������',
  `tags` varchar(128) COLLATE utf8_bin DEFAULT NULL COMMENT '�������� �������� ���� ��������� (����)',
  `metadesc` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `metakeyw` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `isallowvote` tinyint(1) DEFAULT NULL,
  `votes_count` int(11) DEFAULT NULL,
  `votes` float DEFAULT NULL,
  `rightbyif` tinyint(1) DEFAULT NULL COMMENT '����������� ����',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  FULLTEXT KEY `wd_fulltext` (`body`,`announce`,`caption`,`metadesc`,`metakeyw`,`smallcaption`,`tags`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=2365 COMMENT='�������' AUTO_INCREMENT=15 ;

ALTER TABLE `web_news` CHANGE `urlcaption` `url_name` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;
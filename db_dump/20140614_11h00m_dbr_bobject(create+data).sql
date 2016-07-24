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
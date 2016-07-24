<?php

/**
 * Аудит событий
 */
class Audit {
	
	/** Авторизация */
	const SAS_EV_AUTH = 1;
	
	/** Успешный вход */
	const SAS_EV_AUTH_LOGIN = 1;
	/** Не верный логин/пароль */
	const SAS_EV_AUTH_INVALID = 2;
	/** Выход из системы */
	const SAS_EV_AUTH_LOGOUT = 3;
	/** Попытка входа под заблокированной учётной записью */
	const SAS_EV_AUTH_INVALIDBANNED = 4;
	/** Попытка входа с приостановленной учётной записью */
	const SAS_EV_AUTH_INVALIDSUSPENDED = 5;

	/** Доступе к объектам */
	const SAS_EV_ACCESS = 2;
	
	/** Нет доступа к веб-приложению */
	const SAS_EV_ACCESS_NO_APPLICATION = 1;
	/** Нет доступа к модулю (разделу) */
	const SAS_EV_ACCESS_NO_MODULE = 2;
	/** Нет доступа к бизнес-объекту */
	const SAS_EV_ACCESS_NO_BOBJECT = 3;
	/** Нет доступа к действию */
	const SAS_EV_ACCESS_NO_ACTION = 4;

	/** Действия с записями (редактирование)/ПД */
	const SAS_EV_EDIT = 3;

	/** Добавление записи */
	const SAS_EV_EDIT_ADD = 1;
	/** Изменение записи */
	const SAS_EV_EDIT_EDIT = 2;
	/** Перемещение записи */
	const SAS_EV_EDIT_MOVE = 3;
	/** Копирование записи */
	const SAS_EV_EDIT_COPY = 4;
	/** Удаление записи */
	const SAS_EV_EDIT_DELETE = 5;
	/** Выполнение пользовательского действия */
	const SAS_EV_EDIT_USERACTION = 6;
	/** Fast-модификация */
	const SAS_EV_EDIT_FASTMOD = 7;
	/** Изменение порядкового номера записей */
	const SAS_EV_EDIT_CHANGENUMBER = 8;
	/** Создание записи на основе другой (копирование) */
	const SAS_EV_EDIT_ADDBYBASE = 9;
	/** Автоматическое создание записи (алгоритмом) */
	const SAS_EV_EDIT_AUTOADD = 10;
	/** Автоматическое изменение записи (алгоритмом) */
	const SAS_EV_EDIT_AUTOEDIT = 11;
	/** Автоматическое удаление записи (алгоритмом) */
	const SAS_EV_EDIT_AUTODELETE = 12;
	/** Прикрепление файла (загрузка) */
	const SAS_EV_EDIT_ADDFILE = 13;
	/** Редактирование прикреплённого файла (информ.) */
	const SAS_EV_EDIT_EDITFILE = 14;
	/** Удаление прикреплённого файла */
	const SAS_EV_EDIT_DELFILE = 15;
	/** Скачивание прикреплённого файла */
	const SAS_EV_EDIT_DOWNLOADFILE = 16;

	/** Печать отчётов */
	const SAS_EV_REPORT = 5;
	
	/** Простой отчёт (по набору данных) */
	const SAS_EV_REPORT_SIMPLE = 1;

	/** Работа с файлами */
	const SAS_EV_FILES = 6;

	/** Добавление файла */
	const SAS_EV_FILES_ADD = 1;
	/** Удаление файла */
	const SAS_EV_FILES_DELETE = 2;
	/** Удаление записи о не существующем файле */
	const SAS_EV_FILES_DELETE_WITHOUTFILE = 3;
	/** Изменение прикреплённого файла */
	const SAS_EV_FILES_EDIT = 4;
	/** Скачивание защищённого файла */
	const SAS_EV_FILES_DOWNLOAD = 5;
	/** Автом.удаление файла */
	const SAS_EV_FILES_AUTODELETE = 6;
	/** Автом.удаление записи о не существующем файле */
	const SAS_EV_FILES_AUTODELETE_WITHOUTFILE = 7;

	/** Системные */
	const SAS_EV_SYSTEM = 9;
	
	/** Ошибка в SQL-запросе */
	const SAS_EV_SYSTEM_ERRORSQL = 1;

	/**
	 * function addEvent() - Регистрация события в системе
	 * @param int $class_id ИД класса (также CatalogID)
	 * @param int $rec_id ИД записи БО
	 * @param int $evt Номер события
	 * @param int $evtdet Субномер события
	 * @param int $key_id=null Дополнительный ИД
	 * @param String $comment=null Комментарий
	 * @param int $session_id=null ИД сессии, если надо переопределить
	 * @param int $puttime=null Время события, если надо собственное
	 * @return int ИД зарегистрированного события
	 */
	static public function add($class_id, $rec_id, $evt, $evtdet, $key_id = null, $comment = null, $session_id = null, $puttime = null) {

		$evt_id = (int)DB::getValues('id', 'sas_event', 'number = '.$evt);
		if (!$session_id) {
			$session_id = Session::getSessionID();
			/*if (! $session_id) {
				$session_id = (int)DB::getValues('session_id', 'crs_session', 'sessionid = "'.session_id().'"');
			}*/
		}
		
		if (!$puttime) $puttime = time();
		
		$qr = DB::execute('INSERT INTO sas_audit (session_id, class_id, rec_id, key_id, evdatetime, event_id, evtdetail_id)
			VALUES (?,?,?,?,FROM_UNIXTIME(?),?,?)',
			$session_id, $class_id, $rec_id, $key_id, $puttime, $evt_id,
			($evtdet?DB::getValues('id', 'sas_evtdetail', '(number = '.$evtdet.') and (event_id = '.$evt_id.')'):null));
		
		$audit_id = DB::lastID();
		
		if ( ($class_id) && ($rec_id) ) {
			list($tablename, $sas_fields) = DB::getValues('tablename, sas_fields', 'dbr_bobject', 'cid='.$class_id);
			if ( ($tablename) && ($sas_fields) ) {
				$qr = DB::execute('SELECT '.$sas_fields.' FROM '.$tablename.' WHERE (id = ?)', $rec_id);
				if (DB::rowsCount($qr)) {#jxErr('addEvent(): Error! Запись с ИД='.$rec_id.' уже не существует!');
					$values = DB::fetch_row($qr);
					//$q = count($values);
					$temp = Array();
					foreach ($values as $val) {
						if (!$val) continue;
						$_v = substr($val, 0, 24);
						$temp[] = DB::getColumnName($qr, $i).'="'.trim($_v).(strlen($val)>strlen($_v)?'...':'').'"';
					}
					/*for ($i = 0; $i < $q; $i++) {
						$v = $values[$i];
						if (!$v) continue;
						$_v = substr($v,0,24);
						$temp[] = DB::getColumnName($qr, $i).'="'.trim($_v).(strlen($v)>strlen($_v)?'...':'').'"';
					}*/
					$comment = implode(', ', $temp).($comment?Utils::rn.$comment:'');
				}
			}
		}
		if ($comment) DB::execute('INSERT INTO sas_audit_comment (audit_id, comment) VALUES (?,?)', $audit_id, $comment);
		return $audit_id;
	}

	/**
	 * function updEvent($event_id, $comment = null, $key_id = null, $evt = null, $evtdet = null, $rec_id = null) - Изменение информации о событии в аудите
	 * @param int $event_id ИД события
	 * @param string=null $comment Новый комментарий
	 * @param int $key_id=null Новый key_id (второй ИД)
	 * @param int $evt=null Другой тип события
	 * @param int $evtdet=null Другой подтип события
	 * @param int $rec_id=null Другой ИД записи (основной ИД)
	 */
	static public function update($event_id, $comment = null, $key_id = null, $evt = null, $evtdet = null, $rec_id = null) {
		if (!$event_id) return null;
		if ( ($comment===null) && ($key_id===null) && ($evtdet===null) && ($evt===null) && ($rec_id===null) ) return null;
		if ($comment) {
			if (DB::getValues('audit_id', 'sas_audit_comment', 'audit_id = '.$event_id)) {
				DB::execute('update sas_audit_comment set comment = ? where (audit_id = ?)', $comment, $event_id);
			} else DB::execute('insert into sas_audit_comment (audit_id, comment) values (?, ?)', $event_id, $comment);
		}
		if ($key_id) DB::execute('update sas_audit set key_id = ? where (id = ?)', $key_id, $event_id);
		if ($evt) {
			$evt_id = (int)DB::getValues('id', 'sas_event', 'number = '.$evt);
			if (!$evt_id) Request::stop('Ошибка! Событие с типом №'.$evt.' не существует в системе!');
			DB::execute('update sas_audit set event_id = ? where (id = ?)', $evt_id, $event_id);
			if ($evtdet) {
				$evtdet_id = (int)DB::getValues('id', 'sas_evtdetail', '(number = '.$evtdet.') and (event_id = '.$evt_id.')');
				if (!$evtdet_id) Request::stop('Ошибка! Событие с типом № '.$evt.' и подтипом №'.$evtdet.' не существует в системе!');
				DB::execute('update sas_audit set evtdetail_id = ? where (id = ?)', $evtdet_id, $event_id);
			}
		}
		if ($rec_id) DB::execute('update sas_audit set rec_id = ? where (id = ?)', $rec_id, $event_id);
	}
}
?>
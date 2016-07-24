<?php

/*
 * Description: Функционал для работы с БД (класс DB)
 * Authors: dbRus Group
 * Create date: 2009-2010 (db3), 2012 (db4), 2013 (db5)
 */

//if (get_magic_quotes_gpc()) stopWork(500, 'Активированы get_magic_quotes_gpc!');

class TSQLParams {
	public $name;
	public $val;
	function __construct($aname, $aval) {
		$this->name = $aname;
		$this->val = $aval;
	}
}

/**
 * Класс для работы с данными в БД
 */
class DB {
	
	static private $instance = null;
	
	static private $sql;
	static private $params = Array();
	static private $dbc = null;
	static private $flag = 0;
	
	static public function getInstance() {
		if (! self::$instance) {
			self::$instance = new self ( );
		}
		return self::$instance;
	}
	
	function __construct() {
		
		/*if (self::$instance) {
			Request::stop('Ошибка! Экземпляр класса DB может быть создан только один раз! Для использования следует использовать метод DB::getInstance()!');
		}*/
		
		self::$dbc = @mysql_connect(ConfigSite::dbserv, ConfigSite::dbuser, ConfigSite::dbpw);
		if (!self::$dbc) {
			Request::stop(
				'Не возможно подключиться к базе данных!<br/>Возможно указан не верный сервер БД или пароль, либо сервер перегружен.<br/>Попробуйте обратиться к сайту позже.',
				500,
				'Ошибка подключения к БД!'
			);
		}
		
		mysql_query('SET NAMES utf8') or exit('MySQL: SET NAMES Error');
		
		if (!@mysql_select_db(ConfigSite::dbname, self::$dbc)) {
			Request::stop(
				'Ошибка при подключении к базе данных информационного узла!<br/>Возможно запрашиваемая БД "'.ConfigSite::dbname.'" не существует!',
				500,
				'Ошибка подключения к БД!'
			);
		}
		
		//if (!self::$instance) self::$instance = $this;
	}
	
	static private function setParam($pname, $pval) {
		$q = count(self::$params);
		for ($i = 0; $i < $q; $i++) {
			if (self::$params[$i]->name == $pname) {
				self::$params[$i]->val = $pval;
				return;
			}
		}
		$q = count(self::$params);
		self::$params[$q] = new TSQLParams($pname, $pval);
	}
	
	static private function setParams($params) {

		$qp = count($params);
		if ($qp == 2) {
			$p = $params[1];
			if (is_object($p)) {
				$obj = clone $p;
				$i = 0;
				foreach ($obj as $key => $val) $params[++$i] = $val;
				$qp = $i + 1;
			} else if (is_array($p)) {
				$i = 0;
				$q = count($p);
				for ($i = 0; $i < $q; $i++) $params[$i+1] = $p[$i];
				$qp = $q + 1;
			}
		}

		self::clearParams();
		$q = $qp - self::$flag;
		$qc = substr_count(self::$sql, '?');
		if ($qc) {
			if ($q - 1 != $qc) {
				self::genErr('Не совпадает количество переданных в SQL-запрос параметров ('.($q-1).') и найденных параметров в запросе ('.$qc.')!');
			}
			$p = 0;
			for ($i = 0; $i < $qc; $i++) {
				$p = strpos(self::$sql, '?', $p);
				$v = self::getPrepVal($params[self::$flag + $i + 1]);
				self::$sql = substr_replace(self::$sql, $v, $p, 1);
				$p += strlen($v);
			}
		} else {
			if ($q == 2) {
				if (!preg_match_all('/:([\\w]+)/i', self::$sql, $matches)) self::genErr('Не найден параметр в запросе по формату :CODE!');
				$q = count($matches[1]);
				for ($i = 0; $i < $q; $i++) $this->setParam($matches[1][$i], $params[self::$flag + $i+1]);
			} else {
				for ($i = 1; $i < $q; $i+=2) $this->setParam($params[self::$flag + $i], $params[self::$flag + $i+1]);
			}
		}
	}
	
	/**
	 * Выполнить SQL-запрос
	 * @param string $asql SQL-запрос
	 * @return mixed
	 */
	static public function execute($asql = null) {
		
		if (! self::$instance) self::getInstance();
		
		if ($asql) {
			self::$sql = $asql;
			self::$flag = 0;
			self::setParams(func_get_args());
		}
		$q = count(self::$params);
		for ($i = 0; $i < $q; $i++) {
			$p = self::$params[$i];
			self::$sql = str_replace(':'.$p->name, self::getPrepVal($p->val), self::$sql);
		}
	
		$qr = null;
		
		try {
			$start = microtime(true);
			$qr = mysql_query(self::$sql, self::$dbc);
			if (!$qr) throw new Exception;
			$finish = microtime(true);
		} catch ( Exception $e ) {
			Logger::append($e, mysql_error() . Utils::rn . 'SQL:' . Utils::rn . self::$sql);
			RETURN self::genErr( mysql_error() . '<br/>SQL: '.self::$sql, $e );
		}
		
		return $qr;
	}
	
	/**
	 * Получить кол-во записей в выборке
	 * @param object $query
	 * @return int
	 */
	static public function rowsCount($query) {
		if (!$query) self::genErr('Не выполнен execute() для получения количества записей!');
		return mysql_num_rows($query);
	}
	
	/**
	 * Получить кол-во столбцов в выборке
	 * @param object $query
	 * @return int
	 */
	static public function fieldsCount($query) {
		if (!$query) self::genErr('Не выполнен execute() для получения количества полей!');
		return mysql_num_fields($query);
	}
	
	/**
	 * Получить имя столбца по его индексу
	 * @param int $index
	 * @param object $query
	 * @return string
	 */
	static public function getColumnName($index, $query) {
		if (!$query) self::genErr('Не выполнен execute() для получения имени столбца!');
		return mysql_field_name($query, $index);
	}
	
	/**
	 * Получить новый ИД
	 * @param string $table
	 * @param string $fieldkey
	 * @return int
	 */
	static public function genID($table = null, $fieldkey = 'id') {
		
		if (! self::$instance) self::getInstance();
		
		//global $_MSF;
		//if ((!$table) && (@$_MSF)) $table = $_MSF->table;
		
		if (! $table) self::genErr('Не указан параметр $table при вызове метода DB::genID(..)!');
		
		try {
			$qr = mysql_query('insert into '.$table.' ('.$fieldkey.') values (null)', self::$dbc);
			if ($qr) $id = mysql_insert_id(self::$dbc);
		} catch (Exception $e) {
			Logger::append($e, self::$sql);
			RETURN self::$genErr('Не удалось получить новый ИД для таблицы "'.$table.'" с ключевым полем "'.$fieldkey.'"', $e);
		}
		mysql_query('delete from '.$table.' where ('.$fieldkey.' = '.$id.')', self::$dbc);
		return $id;
	}
	
	/**
	 * Получить первый свободный положительный ИД
	 * @param string $table
	 * @param string $fieldkey
	 * @return int
	 */
	static public function genFreeID($table = null, $fieldkey = 'id') {
		
		if (! self::$instance) self::getInstance();
		
		//global $_MSF;
		//if ((!$table) && (@$_MSF)) $table = $_MSF->table;
		
		if (!$table) self::genErr('Не указан параметр $table при вызове метода DB::genNearID(..)!');
		
		$qr = mysql_query('select 0 '.$fieldkey.' from '.$table.' where(not exists(select '.$fieldkey.' from '.$table.' where ('.$fieldkey.' = 1)))
			union
			select '.$fieldkey.' from '.$table.' where ('.$fieldkey.' + 1 not in (select '.$fieldkey.' from '.$table.')) order by '.$fieldkey.' limit 1', self::$dbc);
		list($id) = mysql_fetch_row($qr);
		return ++$id;
	}
	
	/**
	 * Получить ИД последней вставленной записи
	 * @return int
	 */
	static public function lastID() {
		$id = mysql_insert_id(self::$dbc);
		if (!$id) return FALSE;
		return $id;
	}
	
	/**
	 * Получить кол-во затронутых строк в последний SQL-запрос
	 * @return int
	 */
	static public function affectedRows() {
		return mysql_affected_rows(self::$dbc);
	}
	
	
	static private function fetch_pre($query, $s) {
		if (!$query) return self::genErr('Метод fetch'.$s.'($query): Скорее всего был передан нулевой $query!');
		if ($query === true) return self::genErr('Метод fetch'.$s.'($query): Скорее всего текущий SLQ-запрос является запросом на модицикацию данных!');
		if (!is_resource($query)) return self::genErr('Метод fetch'.$s.'($query): Передан не верный параметр, который не является объектом!');
		return true;
	}
	
	/**
	 * Получить следующую запись в виде ассоц.массива
	 * @param object $qr
	 * @return array
	 */
	static public function fetch($query) {
		if (self::fetch_pre($query, '')) return mysql_fetch_assoc($query);
	}
	
	/**
	 * Получить следующую запись в виде индексного массива
	 * @param object $qr
	 * @return array
	 */
	static public function fetch_row($query) {
		if (self::fetch_pre($query, 'row')) return mysql_fetch_row($query);
	}
	
	/**
	 * Получить следующую запись в виде объекта
	 * @param object $qr
	 * @return object
	 */
	static public function fetch_object($query) {
		if (self::fetch_pre($query, 'object')) return mysql_fetch_object($query);
	}

	/**
	 * Получить одно единственное значение
	 * @param object $qr
	 * @return string
	 */
	static public function fetch_val($query) {
		if (!self::fetch_pre($query, '_val')) return null;
		$row = mysql_fetch_row($query);
		if ( ($row === null) || ($row === false) ) return null;
		return $row[0];
	}
	
	/**
	 * Нормализовать значение параметра (защита от SQL-инъекций)
	 * @param mixed $v
	 * @return int|string
	 */
	static private function getPrepVal($v) {
		if (is_numeric($v)) return str_replace(',', '.', $v);
		#if (is_string($v)) return ($v=='null'?'null':'\''.str_replace('?', '%QQQ2%', str_replace('%QUOT2%', '\\\'', str_replace('\'', '"', str_replace('\\\'', '%QUOT2%', $v)))).'\'');
		if (is_string($v)) return ($v=='null'?'null':'\''.str_replace('%QUOT2%', '\\\'', str_replace('\'', '"', str_replace('\\\'', '%QUOT2%', $v))).'\'');
			elseif ((is_bool($v)) && (!$v)) return 0;
			elseif ($v === null) return 'null';
			else return $v;
	}

	static private function clearParams() {
		$q = count(self::$params);
		for ($i = 0; $i < $q; $i++) unset(self::$params[$i]);
		self::$params = Array();
	}
	
	/**
	 * function getValues() - Получить значения полей в записи таблицы
	 * @param string $field Поля в строке SQL-запроса
	 * @param string $table Таблицы в строке SQL-запроса
	 * @param string $cond=null Параметры в строке SQL-запроса
	 * @param string/array $cond_value=null Значения подстановочных параметров в простом запросе с условием
	 * @return string/array
	 */
	static public function getValues($field, $table = '', $cond = '', $cond_values = null) {
		
		if (! self::$instance) self::getInstance();
		
		//global $_MSF;

		// TODO: Проверку на кавычки ' и " сделать, что бы избежать инъекций
		# select xxx from ttt where (fff = "") => select xxx from ttt where (fff = "[\"); delete from ttt where (" "=" ]");
		#$a = explode('"', $cond);
		#for ($i = 0; $i < count($a); $i++) if (($i % 2 == 0) && (strpos($a[$i], ';') !== false)) exit('{"err":"Error! SQL-attack was detected!"}');
		
		$sql = 'SELECT '.$field.($table?' FROM '.$table.($cond?' WHERE ('.$cond.')':''):'').';';
		
		if ($cond_values) {
			if (! is_array($cond_values)) $cond_values = Array($cond_values);
			$p = 0;
			foreach ($cond_values as $key => $val) {
				if (! is_numeric($val)) $val = '"'.addslashes($val).'"';
				$p = strpos($sql, '?', $p);
				if ($p === false) {
					Request::stop('DB::getValues() - Ошибка! Количество подстановочных параметров в запросе отличается от количества передаваемых параметров!');
				}
				$sql = substr_replace($sql, $val, $p, 1);
				$p += strlen($val);
			}
		}
		
		if (strpos($sql, '?') !== false) {
			Request::stop('DB::getValues() - Ошибка! Количество подстановочных параметров в запросе больше, чем количество переданных параметров!<br/>Возможно параметры не были переданы массивом!<br/>Запрос: ' . $sql);
		}
		
		/*if ( ($cond_value) && (!is_numeric($cond_value)) ) $cond_value = '"'.addslashes($cond_value).'"';
		if (strpos($cond, '?') !== false) $cond = str_replace('?', $cond_value, $cond);

		$sql = 'SELECT '.$field.' FROM '.$table.($cond?' WHERE ('.$cond.')':'').';';*/

		#if (preg_match('/[\\s]*update[\\s]+|[\\s]*insert[\\s]+into[\\s]+/i', $sql)) exit('Error! function getValFromDB - words Update or Insert into from was detected!');
		//if ($dbg) jxMsg('sql: '.$sql);
		
		try {
			$start = microtime(true);
			$qr = mysql_query($sql);
			if (!$qr) throw new Exception;
			$finish = microtime(true);
		} catch ( Exception $e ) {
			Logger::append($e, $sql);
			RETURN self::genErr( mysql_error(), $e );
		}

		/*$sqr = mysql_query($sql);
		if (!$sqr) {
			$sqlerr = mysql_error();
			//$class_id = (isset($_MSF)?$_MSF->class_id:null);
			//$rec_id = (isset($_MSF)&&($_MSF->pkeyID)?$_MSF->pkeyID:null);
			//if ( (!$rec_id) && ($cond) && (preg_match('/id[\\s]*=[\\s]*([\\d]+)/i', $cond, $matches))) $rec_id = $matches[1];
			//addEvent($class_id, $rec_id, 9, 1, null, mysql_error().' ['.$sql.']');
			
			Request::stop(
				($this->$alg_name?'Алгоритм '.$this->$alg_name.': ':'').$sqlerr.'!<br/><br/>'.rn.'Ошибка в запросе:<br/><br/>'.rn.addslashes(str_replace("\n", ' ', str_replace("\r\n", ' ', $sql))),
				HH_INTERNALERROR,
				'getValFromDB(): Error!'
			);
		}*/
		
		$row = mysql_fetch_row($qr);
		if (mysql_num_fields($qr)==1) return $row[0]; else return $row;
	}
	
	static private function genErr($msg, $e = null) {
		
		$message = '';
		
		try {
			if ( !is_object($e) ) {
				throw new Exception;
			}
		} catch (Exception $e1) {
			$e = $e1;
		}
		
		$message = addslashes('Stack trace:<br/>' . str_replace("\n", '<br/>', $e->getTraceAsString()) . '<br/>Error in module "' . $e->getFile() . '", line: ' . $e->getLine () . '<br/>'.$e->getMessage() );
		
		/*global $_USER, $_MSF;
		$class_id = (isset($_MSF)?$_MSF->class_id:null);
		$rec_id = (isset($_MSF)&&($_MSF->pkeyID)?$_MSF->pkeyID:null);
		if ( (!$rec_id) && ($this->sql) && (preg_match('/where[\\s]+[\\S]*id[\\s]*=[\\s]*([\\d]+)/i', $this->sql, $matches)) ) $rec_id = $matches[1];

		Audit::addEvent($class_id, $rec_id, 9, 1, null, $msg.' ['.$this->sql.']');*/
		/*if ( ($_USER->id) && (@$_MSF) && ($_MSF->lrID) ) {
			$qr = new DB('delete from sec_user_params where (user_id = ?) and (form_id = ?) and (layer_id = ?)', $_USER->user_id, $_MSF->frmID, $_MSF->lrID);
			$qr->free();
		}*/
		
		Request::stop(
			'Error! '.addslashes(str_replace("\n", '<br/>', str_replace("\r\n", '<br/>', $msg))). '<br/>'.$message,
			Request::HH_INTERNALERROR,
			'DB(): Error!'
		);
		
		return false;
	}
}

//new DB();

/*
function getSysParam($pname) { return getValFromDB('pval', 'sys_params', 'pname="'.$pname.'"'); }
function setSysParam($pname, $pval) {
	$qr = new DB('select id from sys_params where (pname = "'.$pname.'");');
	if (!$qr->quanRows()) $qr->execute('insert into sys_params (pname, pval) values ("'.$pname.'", "'.$pval.'");'); else {
		$row = $qr->fetch_row();
		$qr->execute('update sys_params set pval = "'.$pval.'" where (id = '.$row[0].');');
	}
	$qr->free();
	return $pval;
}

function setParam($pname, $pval, $frm_id = null, $lr_id = null, $status = null) {
	global $_USER;
	$frmsql = ($frm_id?'(form_id = '.$frm_id.')':'(form_id is null)');
	$lrsql = ($lr_id?'(layer_id = '.$lr_id.')':'(layer_id is null)');
	$qr = new DB();
	if ((!$pval) && ($pval !== 0)) {
		$qr->execute('delete from sec_user_params where '.$frmsql.' and '.$lrsql.' and (user_id = '.$_USER->id.') and (pname = "'.$pname.'");');
		return;
	}
	$qr->execute('select id from sec_user_params where '.$frmsql.' and '.$lrsql.' and (user_id = '.$_USER->id.') and (pname = "'.$pname.'");');
	if ($qr->quanRows()) {
		$row = $qr->fetch_row();
		$qr->execute('update sec_user_params set pval = "'.$pval.'", status = '.($status?1:'null').' where (id = '.$row[0].');');
	} else {
		$qr->execute('insert into sec_user_params (user_id, form_id, layer_id, pname, pval, status) values ('.$_USER->id.', '.($frm_id?$frm_id:'null').', '.($lr_id?$lr_id:'null').', "'.$pname.'", "'.$pval.'", '.($status?1:'null').');');
	}
	$qr->free();
}

function getParam($pname, $frm_id = null, $lr_id = null, $getstatus = 0) {
	global $_USER;
	$qr = new DB('select pval'.($getstatus?', status':'').' from sec_user_params where '.($frm_id?'(form_id = '.$frm_id.')':'(form_id is null)').' and '.($lr_id?'(layer_id = '.$lr_id.')':' (layer_id is null)').' and (user_id = '.$_USER->id.') and (pname = "'.$pname.'");');
	if (!$qr->quanRows()) return null;
	$row = $qr->fetch_row();
	$qr->free();
	if ($getstatus) return array($row[0], ($row[1]?1:0)); else return $row[0];
}

function delParams($subpn, $_subpn = null) {
	global $_USER;
	$qr = new DB('delete from sec_user_params where (user_id = '.$_USER->id.') and (form_id is null) and (pname like "'.$subpn.'%")'.($_subpn?' and (pname not like "'.$_subpn.'%")':'').';');
	$qr->free();
}*/
?>
<?php
namespace VediX;

include_once 'request.php';

/**
 * Utils
 */
class Utils {
	
	const rn = "\r\n";
	const n = "\n";
	const br = "<br/>\n";

	static public function ampPost($line) {
		if (($line) && (!is_string($line)) && (!is_numeric($line))) {
			RETURN NULL;
		}
		RETURN str_replace('&', '$AMP$', $line);
	}

	/**
	 * Получить значение элемента [$pname] глобального массива $_SERVER и при необходимости обрезать его до $strlen-символов
	 * @param string $pname
	 * @param int $strlen=null
	 * @return string/null
	 */
	static public function _SERVER($pname, $strlen = null) {
		if (isset($_SERVER[$pname])) {
			if ($strlen) {
				RETURN substr($_SERVER[$pname], 0, $strlen);
			} else {
				RETURN $_SERVER[$pname];
			}
		} else {
			RETURN NULL;
		}
	}
	
	/**
	 * Получить значение элемента [$pname] глобального массива $_SESSION и при необходимости обрезать его до $strlen-символов
	 * @param string $pname
	 * @param mixed $pval=null Если передан второй параметр, то выполняется установка значения, причём если $pval=null, то выполняется unset($_SESSION[$pname])
	 * @return mixed
	 */
	static public function _SESSION($pname, $pval = null) {
		if (func_num_args() == 1) { // get
			if (isset($_SESSION[$pname])) {
				RETURN $_SESSION[$pname];
			} else {
				RETURN NULL;
			}
		} else { // set
			if ($pval === null) {
				unset($_SESSION[$pname]);
			} else {
				$_SESSION[$pname] = $pval;
				return $pval;
			}
		}
	}

	/**
	 * Получить значение элемента [$pname] глобального массива $_POST и при необходимости обрезать его до $strlen-символов
	 * @param string $pname
	 * @param int $strlen=null
	 * @return string/null
	 */
	static public function _POST($pname, $strlen = null, $type = 'string') {
		//if (! is_string($pname)) Request::stop('Utils::_POST(..) получил неверный тип $pname: '.gettype($pname).'!', Request::HH_LOGICERROR);
		if (isset($_POST[$pname])) {
			if (is_array($_POST[$pname])) RETURN $_POST[$pname];
			if ($strlen) {
				RETURN addslashes(substr($_POST[$pname], 0, $strlen));
			} else {
				RETURN addslashes($_POST[$pname]);
			}
		} else {
			RETURN NULL;
		}
	}

	/**
	 * Получить значение элемента [$pname] глобального массива $_GET и при необходимости обрезать его до $strlen-символов
	 * @param string $pname
	 * @param int $strlen=null
	 * @return string/null
	 */
	static public function _GET($pname, $strlen = null) {
		if (isset($_GET[$pname])) {
			if ($strlen) {
				RETURN addslashes(substr($_GET[$pname], 0, $strlen));
			} else {
				RETURN addslashes($_GET[$pname]);
			}
		} else {
			RETURN NULL;
		}
	}
	
	/**
	 * Получить значение элемента [$pname] глобального массива $_REQUEST и при необходимости обрезать его до $strlen-символов
	 * @param string $pname
	 * @param int $strlen=null
	 * @return string/null
	 */
	static public function _REQUEST($pname, $strlen = null) {
		if (isset($_REQUEST[$pname])) {
			$v = $_REQUEST[$pname];
			if (is_array($v)) {
				RETURN $v;
			} else {
				if ($strlen) {
					RETURN addslashes(substr($v, 0, $strlen));
				} else {
					RETURN addslashes($v);
				}
			}
		} else {
			RETURN NULL;
		}
	}

	/**
	 * Извлечь субстроку по её номеру в строке с заданным разделителем
	 * @param string $srcline Строка
	 * @param string $number Номер субстроки
	 * @param string $delimiter Разделитель
	 * @return string/null
	 */
	static public function extWord($srcline, $number, $delimiter) {
		$arr = explode($delimiter, $srcline);
		if (isset($arr[$number - 1])) {
			RETURN $arr[$number - 1];
		 } else {
			RETURN NULL;
		}
	}
	
	/**
	 * Разбить строку на элементы с заданными разделителями (любой символ)
	 * @param array $delimiters
	 * @param string $line
	 * @return array
	 */
	static public function multiExplode($delimiters, $line) {
		$ready = str_replace($delimiters, $delimiters[0], $line);
		$launch = explode($delimiters[0], $ready);
		return  $launch;
	}
	
	/**
	* Вывести структуру объекта с тегами <pre> и </pre>
	* @param mixed $obj Объект
	*/
	static public function varDump($obj) {
		echo '<br /><pre>';
		var_dump($obj);
		echo '</pre>';
	}
	
	/**
	 * Получить элемент по его имени/индексу из массива
	 * @param array $array
	 * @param string/number $element_name
	 * @param mixed $default_value
	 * @return mixed Если элемент не существует, то вернёт $default_value=NULL
	 */
	static public function getAElement( &$array, $element_name, $default_value = null) {
		if (isset( $array[$element_name] )) return $array[$element_name];
		return $default_value;
	}
	
	static public function russianDate($dt = null, $format = 'date') {
		if (! $dt) $dt = time(); else $dt = strtotime($dt);
		$date = explode(' ', date('N d m Y H i', $dt));
		$arr_d = Array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');
		$arr_m = Array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
		return $arr_d[$date[0]-1] . '&nbsp;' . $date[1] . '&nbsp;' . $arr_m[(int)$date[2]-1] . '&nbsp;' . $date[3] . ($format == 'datetime' ? ', ' . $date[4] . ':' . $date[5] : '');
	}
	
	static public function getDateStr($docdate) {
		$doctime = $docdate;
		$docdate = date('Y.m.d', strtotime($doctime));
		$today = date('Y.m.d', time());
		$yesterday = date('Y.m.d', time() - 86400);

		if ($docdate == $today) {
			$docdate = 'Сегодня';//, ' . date('H:i', $doctime);
		} else if ($docdate == $yesterday) {
			$docdate = 'Вчера';//, ' . date('H:i', $doctime);
		} else {
			$docdate = self::russianDate($doctime, 'date'); // 'datetime'
		}
		return $docdate;
	}
	
	static public function deleteArrayElementByValue($array, $value) {
		$key = array_search($value, $array);
		if ( (! is_null($key)) && ($key !== false) ) {
			unset($array[$key]);
		}
		return $array;
	}
}
?>
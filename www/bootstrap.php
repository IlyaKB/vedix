<?php

$GLOBALS['stime'] = microtime();

require_once( __DIR__ . '/config.php');

/**
 * Base object-class
 */
class TObject { }

/**
 * Конфигурация модулей сайта
 */
class ConfigCatalog {
	
	static private $instance;
	static private $ini = Array();
	static private $iniAdmin = Array();
	static private $map = null;
	
	public function __construct() {
		if (!self::$instance) self::$instance = $this;
		self::$map = new TObject();
		self::$ini = parse_ini_file(HD_ROOT . 'catalog.ini', true);
		foreach (self::$ini as $key => &$item) {
			if (strpos($key, '/') !== false) {
				$temp = explode('/', $key);
				$a = $temp[0];
				$m = $temp[1];
				if (isset(self::$ini[$a])) {
					if (! isset(self::$map->$m)) {
						self::$map->$m = $a;
					}
					self::$ini[$a][$m] = $item;
					unset(self::$ini[$key]);
				}
			}
		}
		unset($item);
		
		self::$iniAdmin = parse_ini_file(HD_ADMIN . 'admin.ini', true);
		foreach (self::$iniAdmin as $key => &$item) {
			if (strpos($key, '/') !== false) {
				$temp = explode('/', $key);
				$a = $temp[0];
				$m = $temp[1];
				if (isset(self::$iniAdmin[$a])) {
					self::$iniAdmin[$a][$m] = $item;
					unset(self::$iniAdmin[$key]);
				}
			}
		}
		unset($item);
	}
	
	/**
	 * Получить значение опции
	 * @param string $name Имя параметра
	 * @param string $section=null Код веб-приложения, например, site
	 * @param string $subsection=null Код модуля, например, news
	 * @return array/string/null
	 */
	static public function get($name, $section = 'main', $subsection = null) {
		if (! $subsection) { // global
			return Utils::getAElement(self::$ini[$section], $name);
		} else { // application[/module]
			return Utils::getAElement(self::$ini[$section][$subsection], $name);
		}
	}
	
	/**
	 * Получить секцию. Ex: $module = ConfigCatalog::getSection($this->application, $this->module);
	 * @param string $section='main'
	 * @param string $subsection=null
	 * @return array/string/null
	 */
	static public function getSection($section = 'main', $subsection = null) {
		if (! $section) return NULL;
		if (! $subsection) {
			return Utils::getAElement(self::$ini, $section);
		} else if ($subsection == 'main') {
			$arr = Utils::getAElement(self::$ini, $section);
			if (! is_array($arr)) return NULL;
			$result_arr = Array();
			foreach ($arr as $key => &$field) {
				if (is_array($field)) continue;
				$result_arr[$key] = $field;
			}
			unset($field);
			return $result_arr;
		} else {
			$arr = Utils::getAElement(self::$ini, $section);
			if (! is_array($arr)) return NULL;
			return Utils::getAElement($arr, $subsection);
		}
	}
	
	/**
	 * Получить секцию
	 * @param string $subsection=null
	 * @return array/string/null
	 */
	static public function getSectionAdmin($subsection = null) {
		$section = 'admin';
		if (! $subsection) {
			return Utils::getAElement(self::$iniAdmin, $section);
		} else if ($subsection == 'main') {
			$arr = Utils::getAElement(self::$iniAdmin, $section);
			if (! is_array($arr)) return NULL;
			$result_arr = Array();
			foreach ($arr as $key => &$field) {
				if (is_array($field)) continue;
				$result_arr[$key] = $field;
			}
			unset($field);
			return $result_arr;
		} else {
			$arr = Utils::getAElement(self::$iniAdmin, $section);
			if (! is_array($arr)) return NULL;
			return Utils::getAElement($arr, $subsection);
		}
	}
	
	static public function applicationFor( $module ) {
		if (isset(self::$map->$module)) return self::$map->$module;
		if ($module == 'admin') return 'admin';
		return 'unknownApplication';
	}

	static public function getInstance() {
		if (! self::$instance) {
			self::$instance = new self ( );
		}
		return self::$instance;
	}
	
	static public function isApplication($code) {
		return array_key_exists($code, self::$ini);
	}
	
	static public function isModule($application, $code) {
		$app = self::$ini[$application];
		return array_key_exists($code, $app);
	}
}
$_ConfigCatalog = new ConfigCatalog();

require_once( HD_ROOT . 'core/utils.php');
require_once( HD_ROOT . 'core/logger.php');
require_once( HD_ROOT . 'core/request.php');
require_once( HD_ROOT . 'core/cache.php');
require_once( HD_ROOT . 'core/db.php');
require_once( HD_ROOT . 'core/audit.php');
require_once( HD_ROOT . 'core/user.php');
require_once( HD_ROOT . 'core/session.php');
require_once( HD_ROOT . 'core/controller.php');
require_once( HD_ROOT . 'core/model.php');
require_once( HD_ROOT . 'core/page.main.php');
?>
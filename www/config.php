<?php
namespace VediX;

define('DEFAULT_SKIN', 'catalog');
define('DEFAULT_APPLICATION', 'site');

define('HD_ROOT', str_replace('//', '/', str_replace('\\', '/', __DIR__.'/')));
define('HD_ADMIN', HD_ROOT. 'admin/');
define('HD_CORE', HD_ROOT. 'core/');
define('HD_DATA', HD_ROOT. 'data/');
define('HD_CATALOG', HD_ROOT . DEFAULT_SKIN . '/');
define('HD_CACHE', HD_ROOT . 'caches/cache/');

define('WEB_ROOT', '/');
define('WEB_ADMIN', WEB_ROOT . 'admin/');
define('WEB_CATALOG', WEB_ROOT . DEFAULT_SKIN . '/');
define('WEB_DATA', WEB_ROOT . 'data/');
define('WEB_COMMON', WEB_CATALOG . '_common/');

define('MUSTACHE_CACHE_DIR', HD_ROOT . '/caches/mustache');
define('CACHE_DEFAULT_TTL', 60); // дефолтное время кеширования данных

/**
 * STATIC: Параметры сайта и подключения к БД
 */
class ConfigSite {
	const client_id = 1; # ИД клиента в Системе
	const site_id = 1; # ИД сайта в Системе
	const siteprotocol = 'http';
	static public $sitedname = null;
	const siteport = '80';
	const dbtype = 'MySQL'; # СУБД
	const dbserv = 'localhost';
	const dbname = 'vedix';
	const dbuser = 'root';
	const dbpw = '';
	const clientservpw = 'VediX-hf454-Is0jf-NG75f-67fMn'; # Ключ
	
	public function __construct() {
		self::$sitedname = $_SERVER['SERVER_NAME'];
	}
}
new ConfigSite();

/**
 * STATIC: Параметры подключения к серверу приложений (для установки и обновления веб-приложений VediX)
 */
class ConfigServerApplications {
	const dname = 'server.web-applications.ru'; # Доменное имя сервера приложений VediX, на котором обслуживается ваш сайт
	const port = 80; # Порт сервера приложений
	const script_time_out = 58; # Ограничение времени выполнения php-скриптов
	const fsock_open_timeout = 10; # Ограничение времени ожидания соединения с сервером приложений
	const fsock_gets_time_out = 28; # Ограничение времени ожидания ответа от сервера приложений
}

/**
 * STATIC: Параметры сессий
 */
class ConfigSession {
	const sessionbottimeout = 600; // 10 min
	const sessionguesttimeout = 1800; // 30 min
	const sessionusertimeout = 7200; // 2 hours
}
?>
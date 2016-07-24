<?php

/**
 * Request parser
 */
class Request {
	
	static private $instance;
	
	static private $language = 'ru'; // default
	static private $ajax = false;
	static private $jx = null;
	static private $application = 'site'; // default
	static private $module = 'index'; // default
	static private $key = null;
	static private $key_feature = null;
	
	static public $developer_mode = 0; // off
	static public $demo = 0; // off
	
	static private $hd_application = null;
	static private $hd_module = null;
	
	static private $qrtosa = false; // TODO
	
	static public function getInstance() {
		if (! self::$instance) {
			self::$instance = new self ( );
		}
		return self::$instance;
	}
	
	function __construct() {
		if (self::$instance) {
			Request::stop('Ошибка! Экземпляр класса Request может быть создан только один раз! Для использования следует использовать метод Request::getInstance()!');
		}
		if (!self::$instance) self::$instance = $this;
	}
	
	/**
	 * Получить языковую локацию
	 * @return string
	 */
	static public function LANGUAGE() {
		return self::$language;
	}
	
	/**
	 * Если AJAX-запрос, то вернёт true, иначе false
	 * @return boolean
	 */
	static public function AJAX() {
		return self::$ajax;
	}
	
	/**
	 * Получить код JX-команды
	 * @return string
	 */
	static public function JX() {
		return self::$jx;
	}
	
	/**
	 * Получить путь текущего веб-приложения
	 * @return string
	 */
	static public function HD_APPLICATION() {
		return self::$hd_application;
	}
	
	/**
	 * Получить путь текущего модуля веб-приложения
	 * @return string
	 */
	static public function HD_MODULE() {
		return self::$hd_module;
	}
	
	/**
	 * Получить код текущего веб-приложения
	 * @return string
	 */
	static public function APPLICATION() {
		return self::$application;
	}
	
	/**
	 * Получить код текущего модуля веб-приложения
	 * @return string
	 */
	static public function MODULE() {
		return self::$module;
	}
	
	/**
	 * Получить основной (первый) код экземпляра сущности (ИД/Код). Например, для новости может быть задан код "proshlo_pervoe_sobranie", а для опроса идентификатор 12345.
	 * @return string
	 */
	static public function KEY() {
		return self::$key;
	}
	
	/**
	 * Получить второй код экземпляра сущности (ИД/Код). Например, для поста блога может быть задан сначала код категории KEY, а затем url_name поста KEY_FEATURE
	 * @return string
	 */
	static public function KEY_FEATURE() {
		return self::$key_feature;
	}
	
	/**
	 * Если запрос к серверу-приложений, то вернёт true, иначе false
	 * @return boolean
	 */
	static public function QRTOSA() {
		return self::$qrtosa;
	}
	
	/**
	 * Режим разработчика - да/нет
	 * @return boolean
	 */
	static public function DEVELOPER_MODE() {
		return self::$developer_mode;
	}
	
	/**
	 * Режим выдачи - демо/рабочая версия
	 * @return boolean
	 */
	static public function DEMO() {
		return self::$demo;
	}
	
	/**
	 * Parse request
	 * @return boolean
	 */
	static public function parse() {
		
		$request_uri = $_SERVER ['REQUEST_URI'];
		
		$temp = explode('?', $request_uri);
		$urlparts = preg_replace('/index\.php$/', '', $temp[0]);
		if (!$urlparts) $urlparts = '/';
		$urlparts = substr($urlparts, 1);
		if (substr($urlparts,-1) == '/') $urlparts = substr($urlparts,0,-1);
		
		
		// (/module){0,1}(/key){0,1}
		// example: /news/
		// example: /news/prinyat_zakon_o_rodovih_pomestiyah
		if ($urlparts) {
			
			$urlparts = explode('/', $urlparts);
			
			// Проверка на главную страницу веб-приложения
			if ( (isset($urlparts[0])) && (! isset($urlparts[1])) && (ConfigCatalog::isApplication($urlparts[0])) ) {
				
				self::$application = $urlparts[0];
				self::$module = 'index';
				
			} else if ( (isset($urlparts[0])) && (isset($urlparts[1])) && // application / module / [key] / [key_feature]
					(ConfigCatalog::isApplication($urlparts[0])) && (ConfigCatalog::isModule($urlparts[0], $urlparts[1])) ) { // Проверка на полный формат пути (веб-приложение/модуль/ключ)
				
				self::$application = $urlparts[0];
				self::$module = $urlparts[1];
					
				if (isset($urlparts[2])) { // application / module / key
					self::$key = $urlparts[2];
				}
				
				if (isset($urlparts[3])) { // application / module / key / key_feature
					self::$key_feature = $urlparts[3];
				}
				
			} else { // module / [key] / [key_feature]
			
				if (isset($urlparts[2])) { // module / key / key_feature
					self::$module = $urlparts[0];
					self::$key = $urlparts[1];
					self::$key_feature = $urlparts[2];
				} else if (isset($urlparts[1])) { // module / key
					self::$module = $urlparts[0];
					self::$key = $urlparts[1];
				} else if (isset($urlparts[0])) { // module
					self::$module = $urlparts[0];
				} else {
					self::stop('Не распознан запрос к серверу!', self::HH_NOTFOUNDED, 'Не верный запрос!');
				}

				self::$application = ConfigCatalog::applicationFor( self::$module );
			}
		}
		
		if ( (Utils::_REQUEST('ajax')) || (Utils::_REQUEST('jx')) ) {
			self::$ajax = true;
		}
		
		if (Request::AJAX()) {
			self::$jx = Utils::_REQUEST('jx');
			//exit('language: '.self::$language.Utils::n.'ajax: '.self::$ajax.Utils::n.'jx: '.self::$jx.Utils::n.'application: '.self::$application.Utils::n.'module: '.self::$module); // TODO: del
		}
		
		self::$hd_application = HD_CATALOG . self::APPLICATION() . '/';
		self::$hd_module = self::$hd_application . self::MODULE() . '/';
		
		self::$developer_mode = (ConfigCatalog::get('developer_mode', 'main') ? true : false);
		
		return true;
	}
	
	const HH_ACCESSERROR = 403; # 403 Forbidden
	const HH_NOTFOUNDED = 404; # 404 Not Found
	const HH_LOGICERROR = 422; # 422 Unprocessable Entity
	const HH_INTERNALERROR = 500; # 500 Internal Server Error
	/**
	 * function stop() - Прервать обработку запроса, отобразив нужное сообщение в браузере
	 * @param int/string $header Код заголовка (поддерживаются 200, 301, 403, 404, 422, 500, 503, 507)
	 * @param string $title Название страницы (текст в тегах <title></title>)
	 * @param string/object/array $text=null Полный текст сообщения
	 * @param string $caption=null Заголовок над полным текстом. Если null, то $caption = $title.
	 * @param string $heads='' HTML-код в секции HEAD
	 * @see Расшифровка некоторых кодов HTTP-заголовков:<br/><br/>
	  &bull; <b>200 OK</b> - Успешный запрос<br/>
	  &bull; <b>301 Moved Permanently</b> - Запрошенный документ был окончательно перенесен на новый URI, указанный в поле Location заголовка<br/>
	  &bull; <b>403 Forbidden</b> - Сервер понял запрос, но он отказывается его выполнять из-за каких-то ограничений в доступе<br/>
	  &bull; <b>404 Not Found</b> - Сервер понял запрос, но не нашёл соответствующего ресурса по указанному URI<br/>
	  &bull; <b>422 Unprocessable Entity</b> - Имеется какая-то логическая ошибка из-за которой невозможно произвести операцию над ресурсом<br/>
	  &bull; <b>500 Internal Server Error</b> - Любая внутренняя ошибка сервера, которая не входит в рамки остальных ошибок класса 5xx<br/>
	  &bull; <b>503 Service Unavailable</b> - Сервер временно не имеет возможности обрабатывать запросы по техническим причинам<br/>
	  &bull; <b>507 Insufficient Storage</b> - Не хватает места для выполнения текущего запроса. Проблема может быть временной
	 */
	static public function stop($text, $header = 200, $title = null, $h1_title = null, $heads = '') {

		if (!$h1_title) {
			$h1_title = $title;
		}
		
		if (!$text) {
			$text = 'Сообщение: ' . $title;
		}
		
		if (Request::AJAX()) {
			$text = str_replace('<br>', Utils::rn, str_replace('<br/>', Utils::rn, $text));
		} else {
			$text = str_replace(Utils::n, '<br/>', str_replace(Utils::rn, '<br/>', $text));
		}
		
		if (!$header) {
			$header = 200;
		}
		
		$htext = $header;
		
		if (is_string($header)) {
			header($header);
		}
		
		if (is_numeric($header)) {
			switch ($header) {
				case 200:
					header('HTTP/1.0 200 OK');
					$htext = '';
					break;
				case 301: $htext = '301 Moved Permanently';
					break;
				case 403: $htext = '403 Forbidden';
					break;
				case 404: $htext = '404 Not Found';
					break;
				case 422: $htext = '422 Unprocessable Entity';
					break;
				case 500: $htext = '500 Internal Server Error';
					break;
				case 503: $htext = '503 Service Unavailable';
					break;
				case 507: $htext = '507 Insufficient Storage';
					break;
			}
			if ($htext) {
				if ( Request::AJAX() ) {
					header('JX-Header: HTTP/1.0 ' . $htext);
				} else {
					header('HTTP/1.0 ' . $htext);
				}
			}
		}
		
		$http_referer = Utils::_SERVER('HTTP_REFERER');
		if (strpos(strtolower($http_referer), ConfigSite::$sitedname) === false) $http_referer = null;

		ob_start('stop_replace');
		?><html>
			<head>
				<title>Система управления сайтом <?php echo ($title ? ' - ' . $title : ''); ?></title>
		<?php echo $heads; ?>
				<style>
					* { font: 8pt Comic Sans MS, Arial}
					.nw { white-space: nowrap}
					a {
						color: blue;
						text-decoration: none;
					}
					a:hover {
						color: red;
						text-decoration: underline;
					}
					pre {
						color: black;
					}
				</style>
			</head>
			<body>
				<table cellspacing="0px" class="tblStopWork"><tr>
						<td width="50%"> </td>
						<td style="border: 1px solid #38a; background-color: #faf8f5">
							<h1 style="font: bold 12pt Arial; border: 1px outset #aaa; margin: 0px; padding: 5px; text-align: center; white-space: nowrap">
		<?php echo $h1_title; ?>
							</h1>
							<div style="text-align: center; padding: 10px; border-bottom: 1px solid gray">
								<?php if ($http_referer) echo '<a style="font: bold 10pt Arial" href="'.$http_referer.'">&lt;&lt; На предыдущую страницу</a>  или ';?>
								<a style="font: bold 10pt Arial" href="/"><?php echo ($http_referer?'':'&lt;&lt;');?> Вернуться на главную страницу сайта<?php echo ($http_referer?' &gt;&gt;':'');?></a>
							</div>
		<?php if ($text) { ?>
								<div style="width: 572px; height: 325px; padding: 10px; text-align: left; font: bold 10pt Arial; color: #58c; background-color: #fcfbfa; overflow: scroll">
			<?php echo (is_string($text) ? '<div style="font: 8pt Arial; text-align: center" style="margin-bottom: 2px">Размер полученного ответа от ' . ConfigSite::$sitedname . ': ' . number_format(strlen($text), 0, '.', ' ') . ' байт</div><br/>' : ''); ?>
			<?php echo (is_object($text) || is_array($text) ? Utils::varDump($text) : stripslashes( Request::AJAX() ? str_replace("\r\n", '<br/>', $text) : $text ) ); ?>
									<br/><br/><pre>Стёк вызовов функций:<br/><?php debug_print_backtrace();?></pre>
								</div>
		<?php } ?>
							<div style="text-align: center; padding: 5px; color: #f00; font: bold 10pt Arial" title="Код ошибки на <?php echo ConfigSite::$sitedname; ?>">
		<?php if ($htext) { ?>
							<?php echo (self::QRTOSA() ? 'Сервер-приложений: ' : 'Веб-сервер: ') . $htext; ?>
		<?php } ?>
								<span style="color: #888">(time=<?php echo  Request::getTime();?> с)</span>
							</div>
						</td>
						<td width="50%"> </td>
					</tr></table>
			</body>
		</html>
		<?php
		ob_end_flush();
		EXIT();
	}
	
	/**
	 * Преобразовать объект/массив в JSON-строку и отправить клиенту в виде одного единственного ответа веб-сервера (делается EXIT)
	 * @param object/array $data
	 */
	static public function stopAjax( $data ) {
		header('JX-Header: HTTP/1.0 OK');
		EXIT( json_encode($data) );
	}
	
	/**
	 * Остановить обработку запроса веб-сервером и отправить только сообщение об ошибке (+время)
	 * @param string $message
	 */
	static public function stopError( $message ) {
		self::stopAjax(
			Array(
				'err' => str_replace(Utils::br, '\n', str_replace(Utils::n, '\n', str_replace(Utils::rn, '\n', $message))),
				'time' => Request::getTime()
			)
		);
	}
	
	/**
	 * Остановить обработку запроса веб-сервером и отправить только сообщение (+время)
	 * @param string $message
	 */
	static public function stopMessage( $message ) {
		self::stopAjax(
			Array(
				'msg' => str_replace(Utils::br, '\n', str_replace(Utils::n, '\n', str_replace(Utils::rn, '\n', $message))),
				'time' => Request::getTime()
			)
		);
	}
	
	static public function getArray () {
		
		$arr = Array();
		
		$arr['language'] = self::$language;
		$arr['ajax'] = self::$ajax;
		$arr['jx'] = self::$jx;
		$arr['application'] = self::$application;
		$arr['module'] = self::$module;
		$arr['pagecode'] = self::$key;
		$arr['component'] = self::$key;
		$arr['key'] = self::$key;
		
		return $arr;
	}
	
	/**
	 * Время обработки запроса веб-сервером (за вычетом работы Smarty)
	 * @return float
	 */
	static public function getTime() {
		$stime = Utils::extWord($GLOBALS['stime'], 2, ' ').'.'.Utils::extWord(Utils::extWord($GLOBALS['stime'], 1, ' '), 2, '.');
		$etime = microtime();
		$etime = Utils::extWord($etime, 2, ' ').'.'.Utils::extWord(Utils::extWord($etime, 1, ' '), 2, '.');
		return round($etime - $stime, 3);
	}
}

function stop_replace($buffer){
	RETURN preg_replace('/#0[\s\S]+?called/i', '#0 Request::stop(...) called', str_replace('\"', '"', $buffer));
}

new Request();

if ( ! Request::parse() ) Request::stop('Страница с запрашиваемым адресом не существует!', Request::HH_NOTFOUNDED, 'Страница не существует!');
?>
<?php
namespace VediX;

/**
 * Session class
 */
class Session {
	
	static private $instance;
	
	/**
	 * crs_session.session_id or crs_session_history.id
	 * @var int
	 */
	static private $id = null;
	
	/**
	 * IP-адрес
	 * @var string
	 */
	static private $ip = null;
	
	/**
	 * ИД IP-адреса
	 * @var int
	 */
	static private $ip_id = null;
	
	/**
	 * Текстовый ИД сессии
	 * @var string
	 */
	static private $sessionid = null;
	
	/**
	 * Агент (браузер)
	 * @var string
	 */
	static private $agent = null;
	
	/**
	 * ИД агента
	 * @var int
	 */
	static private $agent_id = null;
	
	/**
	 * Определитель типа девайса и его некоторых параметров
	 * @var object
	 */
	static public $mobile_detect = null; // TODO: del or next
	static public $uagent_info = null; // TODO: del or previous
	
	/**
	 * HTTP_REFERER
	 * @var string
	 */
	static private $http_referer = null;
	
	/**
	 * Время длительности сессии
	 * @var int
	 */
	static private $timeout = ConfigSession::sessionbottimeout;
	
	/**
	 * ИД хоста
	 * @var int
	 */
	static private $host_id = null;
	
	/**
	 * Имя хоста
	 * @var string 
	 */
	static private $host_name = null;
	
	const ENV1 =  'REMOTE_ADDR';
	const ENV2 = 'HTTP_CLIENT_IP';
	const ENV3 = 'HTTP_X_FORWARDED_FOR';
	const UNK = 'unknown';
	
	static public function getInstance() {
		if (! self::$instance) {
			self::$instance = new self ( );
		}
		return self::$instance;
	}
	
	function __construct() {
		
		if (self::$instance) {
			Request::stop('Ошибка! Экземпляр класса Session может быть создан только один раз! Для использования следует использовать метод Session::getInstance()!');
		}
		if (!self::$instance) self::$instance = $this;
		
		self::initIPSession();
		
		self::checkDemo();
		
		//$User = User::getInstance();
		
		if ( (! self::$ip) || (self::$ip == self::UNK) ) {
			Request::stop(
				'Доступ запрещён - Система не смогла определить ваш IP-адрес!',
				HH_ACCESSERROR,
				'Доступ запрещён!'
			);
		}
		
		self::$ip_id = (int)DB::getValues('id', 'crs_ip', 'ip = "'.self::$ip.'"');

		if (! self::$ip_id) {
			DB::execute('INSERT INTO crs_ip (ip, firstdate, lastdate, sesquan, quanqr) VALUES (?,NOW(), NOW(),0,0)', Session::$ip);
			self::$ip_id = DB::lastID();
		}
		
		self::$host_name = $_SERVER['SERVER_NAME'];
		self::$host_id = $this->getHostIDByName(self::$host_name);

		self::$agent = Utils::_SERVER('HTTP_USER_AGENT', 256);
		if (! self::$agent) {
			Request::stop(
				'Доступ запрещён - Ваш браузер не прошёл идентификацию! Отсутствует или передан пустой USER_AGENT!',
				HH_ACCESSERROR,
				'Доступ запрещён!'
			);
		}
		self::$agent_id = $this->getAgentIDByName(self::$agent);
		
		self::$http_referer = Utils::_SERVER('HTTP_REFERER');
		if (!self::$http_referer) self::$http_referer = Utils::_SERVER('REQUEST_URI');
		

		# Деактивация устаревших сессий
		DB::execute('UPDATE crs_session SET status = 0 WHERE (status = 1) AND (UNIX_TIMESTAMP(puttime) + timeout < '.time().')');
		
		# Удаление деактивированных сессий
		$qr = DB::execute('SELECT session_id FROM crs_session WHERE (status = 0) ORDER BY puttime');
		while ($id = DB::fetch_val($qr)) {
			self::closeSession( $id );
		}
		
		// TODO: детектор взять из jqxResponse
		// TODO: DELETE
		/*include HD_CORE . 'libs/mobile/uaparser2_Mobile_Detect.php';
		self::$mobile_detect = new Mobile_Detect();
		include HD_CORE . 'libs/mobile/uaparser3_uagent_info.php';
		self::$uagent_info = new uagent_info();
		if (! isset($_SESSION['MOBILE_DETECTED'])) {
			$_SESSION['MOBILE_DETECTED'] = (self::$mobile_detect->isMobile() || self::$mobile_detect->isTablet());
			if ($_SESSION['MOBILE_DETECTED']) {
				header ('Location: /mobile/');
				EXIT();
			}
		}*/

		# Получаем текущую сессию
		$qr = DB::execute('
			SELECT
				s.session_id, s.user_id, u.login, u.fullname,
				u.group_id, s.group_code, g.name group_name, u.email, u.isbanned, s.isshowerror, u.regdate, s.isbot, s.timeout,
				u.iswoman, u.photo, u.district, u.locality, u.social_network, u.social_profile, u.bornyear
			FROM crs_session s
				LEFT JOIN sec_user u ON (u.id = s.user_id)
				LEFT JOIN sec_group g ON (g.id = u.group_id)
			WHERE (s.sessionid = ?) AND (s.ip = ?) AND (s.status = 1)', self::$sessionid, self::$ip);

		// Защита от спамботов в виде привязки только к одному IP
		/*//TODO X1001:
		$is2 = false;
		if (!$qr->quanRows()) {
			$qr->execute('SELECT sessionid, session_id, user_id, user_login login, user_fullname fullname, group_id, group_code,
				email, status, isbanned, isshowerror, regdate, isbot, timeout, http_referer, agentname agent
				FROM crs_session WHERE (status = 1) AND (ip = ?) AND (isbot = 1)', self::$ip);
			if ($qr->quanRows()) {
				User::setValues( $qr->fetch() );
				$is2 = true;
				//TODO ?: if (QRTOSA) $_SESSIONID = $_USER->sessionid; else session_id($_USER->sessionid);
			}
		}*/

		if (DB::rowsCount($qr)) {
			
			$row = DB::fetch($qr);
			
			self::setValues( $row );
			User::setValues( $row );

			#if ($_USER->group_id!=1) ini_set('display_errors', false); else error_reporting(E_ALL); // TODO: on

			DB::execute('UPDATE crs_session SET puttime = Now(), quanqr = quanqr + 1 WHERE (session_id = ?)', self::$id);
			DB::execute('UPDATE sec_user SET lastdate = Now(), quanqr = quanqr + 1 WHERE (id = ?)', User::id());

		} else { // Add new session
			self::$timeout = ConfigSession::sessionbottimeout;
			$params = self::doGuest();
			$session_id = self::createSession( $params );
		}

		# Детектор людей и ботов
		if ( (User::$isbot) && (Request::JX() == 'ispeople') ) {
			User::$isbot = null;
			self::$timeout = (User::id()?ConfigSession::sessionusertimeout:ConfigSession::sessionguesttimeout);
			DB::execute('UPDATE crs_session_history SET isbot = NULL, timeout = ? WHERE (id = ?)', self::$timeout, self::$id);
			DB::execute('UPDATE crs_session SET isbot = NULL, timeout = ? WHERE (session_id = ?)', self::$timeout, self::$id);
			Request::stopAjax(1);
		}

		/*
		// Защита от Оперы, когда при восстановлении вкладок браузер генерит разные sessionid на страницы одного и того же сайта
		// TODO: Тут правда проблема - за эти 7-20 секунд можно перехватить сессию с тем же IP и агентом. Как быть? М.б. забить на это плодильство?
		if (!@$_USER->sessionid) {
			$qr->execute('select * from crs_session where (status = 1) and (puttime + '.(int)$_CRS->operasessauthtimeout.' > '.time().') and (ip = "'.$_IP.'") and (agent = "'.str_replace('"', '\\"', $_AGENT).'")');
			$_USER = $qr->fetch_object();
			if (@$_USER->sessionid) session_id($_USER->sessionid);
		}
		*/
	}
	
	/**
	 * Инициализировать User как гостя
	 * @return Array
	 */
	static function doGuest() {
		
		self::initIPSession();
		self::checkDemo();
		
		User::id(0);
		User::$login = 'guest';
		User::$fullname = 'Guest';
		User::$group_id = 0;
		User::$group_code = 'guests';
		User::$email = null;
		User::$isbot = 1;
		User::$isshowerror = false;
		User::$isbanned = false;
		User::$regdate = null;

		return Array(
			'user_id' => User::id(),
			'login' => User::$login,
			'fullname' => User::$fullname,
			'group_id' => User::$group_id,
			'group_code' => User::$group_code,
			'email' => User::$email,
			'isbot' => User::$isbot,
			'isshowerror' => User::$isshowerror,
			'isbanned' => User::$isbanned,
			'regdate' => User::$regdate
		);
	}
	
	static function initIPSession() {
		self::$ip = self::detectIP();	
		if (! session_id()) {
			ini_set('session.use_trans_sid', 0);
			session_start();
		}
		self::$sessionid = session_id();
	}
	
	static function checkDemo() {
		
		if ( (Utils::_GET('demo') === '') || (Utils::_GET('demo')) ) {
			Utils::_SESSION('demo', 1); // demo on
		} else if ( (Utils::_GET('demo') == '0') || (strtolower(Utils::_GET('demo')) == 'off') || (strtolower(Utils::_GET('demo')) == 'false') ) {
			Utils::_SESSION('demo', null); // demo off
		}
		
		Request::$demo = Utils::_SESSION('demo') ? 1 : 0;
	}
	
	static function detectIP() {
		$ip = '';
		if (getenv(self::ENV1) && strcasecmp(getenv(self::ENV1),self::UNK)) $ip = getenv(self::ENV1);
			elseif (getenv(self::ENV2) && strcasecmp(getenv(self::ENV2), self::UNK)) $ip = getenv(self::ENV2);
			elseif (getenv(self::ENV3) && strcasecmp(getenv(self::ENV3), self::UNK)) $ip = getenv(self::ENV3);
			elseif (!empty($_SERVER[self::ENV3]) && strcasecmp($_SERVER[self::ENV3], self::UNK)) $ip = $_SERVER[self::ENV3];
			else $ip = self::UNK;
		return $ip;
	}
	
	/**
	 * Получить ИД агента по его имени. Если агент не зарегистрирован в БД, то он регистрируется
	 * @param string $nameAgent
	 * @return int
	 */
	private function getAgentIDByName($nameAgent) {
		$agent_id = DB::getValues('id', 'crs_agent', 'name="'.$nameAgent.'"');
		if (!$agent_id) {
			$agent_id = DB::genID('crs_agent');
			DB::execute('INSERT INTO crs_agent (id, name) VALUES (?, ?)', $agent_id, $nameAgent);
		}
		return $agent_id;
	}

	/**
	 * Получить ИД хоста по его имени. Если хост не зарегистрирован в БД, то он регистрируется
	 * @param string $nameHost
	 * @return int
	 */
	private function getHostIDByName($nameHost) {
		$host_id = DB::getValues('id', 'crs_host', 'name="'.$nameHost.'"');
		if (!$host_id) {
			$host_id = DB::genID('crs_host');
			DB::execute('INSERT INTO crs_host (id, name) VALUES (?, ?)', $host_id, $nameHost);
		}
		return $host_id;
	}
	
	static public function setValues( $array ) {
		
		if (!is_array( $array ) ) return false;
		
		if (array_key_exists('id', $array)) self::$id = (int)$array['id'];
		if (array_key_exists('session_id', $array)) self::$id = (int)$array['session_id'];
		if (array_key_exists('timeout', $array)) self::$timeout = (int)$array['timeout'];
	}
	
	/**
	 * Создать новую сессию
	 * @param Array $params
	 * @return int Новый ИД сессии в БД
	 */
	static public function createSession( $params = Array() ) {
		
		$current_session_id = self::$id;
		
		if ($current_session_id) {
			self::closeSession( $current_session_id );
		}

		if (! isset($params['user_id'])) $params['user_id'] = 0;
		if (! isset($params['group_id'])) $params['group_id'] = 0;
		if (! isset($params['timeout'])) $params['timeout'] = ConfigSession::sessionbottimeout;
		if (! isset($params['isshowerror'])) $params['isshowerror'] = 0;
		if (! isset($params['login'])) $params['login'] = 'guest';
		if (! isset($params['fullname'])) $params['fullname'] = 'Guest';
		if (! isset($params['group_code'])) $params['group_code'] = 'guests';
		if (! isset($params['email'])) $params['email'] = null;
		if (! isset($params['regdate'])) $params['regdate'] = null;
		if (! isset($params['isbot'])) $params['isbot'] = 1;
		
		self::$timeout = $params['timeout'];

		$qr = DB::execute('INSERT INTO crs_session_history (sessionid, ip_id, user_id, group_id, stime, timeout, status, http_referer,
			agent_id, isbot, host_id, quanqr, quanqr_sa) VALUES (?, ?, ?, ?, NOW(), ?, 1, ?, ?, ?, ?, 0, 0)',
			self::$sessionid, self::$ip_id, $params['user_id'], $params['group_id'], self::$timeout,
			self::$http_referer, self::$agent_id, $params['isbot'], self::$host_id
		);
		
		self::$id = DB::lastID();

		$qr = DB::execute('INSERT INTO crs_session (session_id, sessionid, ip_id, ip, user_id, group_id, stime, timeout, puttime,
			isshowerror, user_login, user_fullname, group_code, email,
			status, isbot, isbanned, regdate, agentname, http_referer, quanqr, quanqr_sa)
			VALUES (?, ?, ?, ?, ?, ?,  NOW(), ?, NOW(), ?, ?, ?, ?, ?, 1, ?, 0, ?, ?, ?, 1, 0)',
			self::$id, self::$sessionid, self::$ip_id, self::$ip, $params['user_id'], $params['group_id'], self::$timeout,
			$params['isshowerror'], $params['login'], $params['fullname'], $params['group_code'], $params['email'],
			$params['isbot'], $params['regdate'], self::$agent, self::$http_referer);
		
		$qr = DB::execute('UPDATE crs_ip SET lastdate = NOW(), sesquan = IFNULL(sesquan, 0) + 1, lastsession_id = ? WHERE (id = ?)',
			Session::$id, Session::$ip_id);
		
		$qr = DB::execute('UPDATE sec_user SET lastdate = NOW(), quan_sessions = IFNULL(quan_sessions, 0) + 1 WHERE (id = ?)',
			$params['user_id']);
		
		return self::$id;
	}

	/**
	 * Закрыть сессию
	 * @param int $session_id
	 */
	static public function closeSession( $session_id ) {

		list($user_id, $puttime, $ip_id, $quanqr, $quanqr_sa)
			= DB::getValues('user_id, UNIX_TIMESTAMP(puttime), ip_id, quanqr, quanqr_sa', 'crs_session', 'session_id = '.$session_id);
		
		if ($user_id) {
			Audit::add(User::class_id_user, $user_id, Audit::SAS_EV_AUTH, Audit::SAS_EV_AUTH_LOGOUT, null, null, $session_id, $puttime);
		}

		$qr = DB::execute('UPDATE crs_session_history SET etime = FROM_UNIXTIME(?), quanqr = ?, quanqr_sa = ?, status = 0 WHERE (id = ?)',
			$puttime, $quanqr, $quanqr_sa, $session_id);

		$qr = DB::execute('UPDATE crs_ip SET lastdate = NOW(), quanqr = IFNULL(quanqr, 0) + ?, quanqr_sa = IFNULL(quanqr_sa, 0) + ?, lastsession_id = ? WHERE (id = ?)',
			$quanqr, $quanqr_sa, $session_id, $ip_id);

		$qr = DB::execute('DELETE FROM crs_session WHERE (session_id = '.$session_id.')');
		
		$_SESSION['authorized'] = false;
	}

	/**
	 * Получить текстовый ИД сессии
	 * @return string
	 */
	static public function getSessionIDText() {
		return self::$sessionid;
	}
	
	/**
	 * Получить ИД сессии в БД
	 * @return int
	 */
	static public function getSessionID() {
		return self::$id;
	}
	
	/**
	 * Установить новый ИД сессии для БД
	 * @return int
	 */
	static public function setSessionID( $session_id ) {
		self::$id = $session_id;
	}
	
	/**
	 * Получить ИД IP-адреса
	 * @return int
	 */
	static public function getIPID() {
		return self::$ip_id;
	}
	
	/**
	 * Получить IP-адрес
	 * @return string
	 */
	static public function getIP() {
		return self::$ip;
	}
	
	/**
	 * Получить ИД хоста
	 * @return int
	 */
	static public function getHostID() {
		return self::$host_id;
	}
	
	/**
	 * Получить ИД агента
	 * @return int
	 */
	static public function getAgentID() {
		return self::$agent_id;
	}
	
	/**
	 * Получить имя агента
	 * @return string
	 */
	static public function getAgent() {
		return self::$agent;
	}
	
	/**
	 * Получить HTTP_REFERER
	 * @return string
	 */
	static public function getHttpReferer() {
		return self::$http_referer;
	}
	
	/**
	 * Получить время длительности сессии
	 * @return int
	 */
	static public function getTimeout() {
		return self::$timeout;
	}
	
	/**
	 * Установить время длительности сессии
	 * @return int
	 */
	static public function setTimeout( $timeout = ConfigSession::sessionbottimeout ) {
		self::$timeout = $timeout;
	}
}

// TODO: кроме админки сессии надо подключать для авторизованного юзера, но без проверки в БД каждый раз!
if (Request::APPLICATION() == 'admin') {
	new Session();
} else {
	ini_set('session.use_trans_sid', 0);
	session_start();
	if (((int)Utils::_SESSION('authorized')) || (Utils::_POST('jx') == 'authenticate')) {
		new Session();
	} else {
		Session::doGuest();
	}
}
?>
<?php
namespace VediX;

/**
 * User class
 */
class User {
	
	static private $instance;
	
	const avatar_default_male = '/data/site/user/avatar_default_0.png';
	const avatar_default_female = '/data/site/user/avatar_default_1.png';
	const avatar_default_unknow = '/data/site/user/avatar_default_x.png';
	
	const pw_suffix = 'Dsanmjgb7u1j'; // Произвольный код для генерации пароля
	
	const group_root = 1;
	const group_guests = 0;
	const group_admins = 2;
	const group_users = 3;
	
	// TODO:
	const class_id_catalog = 32; // Элемент каталога веб-приложений "Элемент"
	const class_id_user = 33; // Элемент каталога веб-приложений "Пользователь"
	
	/**
	 * sec_user.id
	 * @var int
	 */
	static private $id = 0;
	
	/**
	 * sec_user.login
	 * @var string
	 */
	static public $login = 'guest';
	
	/**
	 * sec_user.fullname
	 * @var string
	 */
	static public $fullname = 'Guest';
	
	/**
	 * sec_group.id
	 * @var int
	 */
	static public $group_id = 0;
	
	/**
	 * sec_group.code
	 * @var string
	 */
	static public $group_code = 'guests';
	
	/**
	 * sec_group.name
	 * @var string
	 */
	static public $group_name = 'Гости сайта';
	
	/**
	 * sec_user.email
	 * @var string
	 */
	static public $email = null;
	
	/**
	 * sec_user.phone
	 * @var string
	 */
	static public $phone = null;
	
	/**
	 * sec_user.regdate
	 * @var string
	 */
	static public $regdate = null;
	
	/**
	 * sec_user.bornyear
	 * @var int
	 */
	static public $bornyear = null;
	
	/**
	 * sec_user.bornmonth
	 * @var int
	 */
	static public $bornmonth = null;
	
	/**
	 * sec_user.bornday
	 * @var int
	 */
	static public $bornday = null;
	
	/**
	 * date(year) - sec_user.bornyear
	 * @var int
	 */
	static private $age = null;
	
	/**
	 * sec_user.iswoman
	 * @var bool
	 */
	static public $iswoman = false;
	
	/**
	 * sec_user.locality
	 * @var string
	 */
	static public $locality = null;
	
	/**
	 * sec_user.photo (URL to image)
	 * @var string
	 */
	static public $photo = null;
	
	/**
	 * sec_user.status
	 * @var int
	 */
	static public $status = null;
	
	/**
	 * sec_user.isbanned
	 * @var bool
	 */
	static public $isbanned = 0;
	
	/**
	 * sec_user.isbot
	 * @var bool
	 */
	static public $isbot = 1;
	
	/**
	 * sec_user.isshowerror
	 * @var bool
	 */
	static public $isshowerror = false;
	
	/**
	 * Название соц.сети, под которой зашёл пользователь
	 * @var string
	 */
	static public $social_network = null;
	
	/**
	 * Ссылка на профиль в соц.сети
	 * @var string
	 */
	static public $social_profile = null;
	
	
	static public function getInstance() {
		if (! self::$instance) {
			self::$instance = new self ( );
		}
		return self::$instance;
	}
	
	function __construct() {
		if (self::$instance) {
			Request::stop('Ошибка! Экземпляр класса User может быть создан только один раз! Для использования следует использовать метод User::getInstance()!');
		}
		if (!self::$instance) self::$instance = $this;
	}
	
	static public function setValues( $array ) {
		
		if (! is_array( $array ) ) return false;
		
		if (array_key_exists('id', $array)) self::$id = (int)$array['id'];
		if (array_key_exists('user_id', $array)) self::$id = (int)$array['user_id'];
		if (array_key_exists('login', $array)) self::$login = $array['login'];
		if (array_key_exists('user_login', $array)) self::$login = $array['user_login'];
		if (array_key_exists('fullname', $array)) self::$fullname = $array['fullname'];
		if (array_key_exists('user_fullname', $array)) self::$fullname = $array['user_fullname'];
		if (array_key_exists('group_id', $array)) self::$group_id = $array['group_id'];
		if (array_key_exists('group_code', $array)) self::$group_code = $array['group_code'];
		if (array_key_exists('group_name', $array)) self::$group_name = $array['group_name'];
		if (array_key_exists('email', $array)) self::$email = $array['email'];
		if (array_key_exists('phone', $array)) self::$email = $array['phone'];
		if (array_key_exists('regdate', $array)) self::$regdate = $array['regdate'];
		if (array_key_exists('bornyear', $array)) self::$bornyear = $array['bornyear'];
		if (array_key_exists('bornmonth', $array)) self::$bornmonth = $array['bornmonth'];
		if (array_key_exists('bornday', $array)) self::$bornday = $array['bornday'];
		if (array_key_exists('iswoman', $array)) self::$iswoman = $array['iswoman'];
		if (array_key_exists('locality', $array)) self::$locality = $array['locality'];
		if (array_key_exists('photo', $array)) self::$photo = $array['photo'];
		if (array_key_exists('status', $array)) self::$status = $array['status'];
		if (array_key_exists('user_status', $array)) self::$status = $array['user_status'];
		if (array_key_exists('isbanned', $array)) self::$isbanned = (int)$array['isbanned'];
		if (array_key_exists('isbot', $array)) self::$isbot = (int)$array['isbot'];
		if (array_key_exists('isshowerror', $array)) self::$isshowerror = (int)$array['isshowerror'];
		if (array_key_exists('social_network', $array)) self::$social_network = $array['social_network'];
		if (array_key_exists('social_profile', $array)) self::$social_profile = $array['social_profile'];
		
		if (! self::$login) self::$login = 'guest';
		if (! self::$group_code) self::$group_code = 'Guests';
		
		if (! self::$photo) {
			if (self::$iswoman) self::$photo = self::avatar_default_female;
				else if (self::$iswoman === null) self::$photo = self::avatar_default_unknow;
				else self::$photo = self::avatar_default_male;
		}
		
		if (! self::$bornmonth) self::$bornmonth = 1;
		if (! self::$bornday) self::$bornday = 1;
		
		if (self::$bornyear) {
			$borndate =  self::$bornday . '.' . self::$bornmonth . '.' . self::$bornyear;
			$diff = strtotime('now') - strtotime($borndate);
			self::$age = min(255, max(3, (int)($diff / 31557600 - 1))); // 31557600 = 60*60*24*365.25
		}
	}
	
	static public function getArray () {
		
		$arr = Array();
		
		$arr['id'] = self::$id;
		$arr['login'] = self::$login;
		$arr['fullname'] = self::$fullname;
		$arr['group_id'] = self::$group_id;
		$arr['group_code'] = self::$group_code;
		$arr['group_name'] = self::$group_name;
		$arr['email'] = self::$email;
		$arr['phone'] = self::$phone;
		$arr['regdate'] = self::$regdate;
		$arr['locality'] = self::$locality;
		$arr['bornyear'] = self::$bornyear;
		$arr['age'] = self::$age;
		$arr['iswoman'] = self::$iswoman;
		$arr['photo'] = self::$photo;
		$arr['status'] = self::$status;
		$arr['isbanned'] = self::$isbanned;
		$arr['isbot'] = self::$isbot;
		$arr['isshowerror'] = self::$isshowerror;
		$arr['social_network'] = self::$social_network;
		$arr['social_profile'] = self::$social_profile;
		
		return $arr;
	}
	
	/**
	 * Проверить - авторизован ли пользователь или нет
	 * @return bool
	 */
	static public function isAuthorized() {
		return (self::$id?true:false);
	}
	
	/**
	 * Выполнить вход по логину и паролю
	 * @param string $login
	 * @param string $user_password
	 * @return boolean Если успешно, то вернет true, иначе false
	 */
	static public function authorize( $login, $user_password) {
		
		$result = new TObject();
		
		if ( ($login) && ($user_password) ) {
			
			$by_email = (strpos($login, '@') === false ? false : true);
			$to_md5 = ( (strlen($user_password)==32) && (preg_replace('/[^0-9a-f]*/i', '', $user_password) == $user_password) ? false : true);
			
			$qr = DB::execute('SELECT
					u.id, u.login, u.fullname, u.email, u.group_id, g.code group_code, g.name group_name, u.status, u.isbanned, g.isshowerror, u.regdate,
					u.bornyear, u.iswoman, u.photo, u.social_network, u.social_profile, u.phone
				FROM sec_user u
					LEFT JOIN sec_group g ON (g.id = u.group_id)
				WHERE (LOWER(u.'.($by_email?'email':'login').') = LOWER(?)) AND (u.pw = ?);', $login, ($to_md5?md5($user_password):$user_password));

			if (! DB::rowsCount($qr)) {
				Audit::add(User::class_id_user, self::$id, Audit::SAS_EV_AUTH, Audit::SAS_EV_AUTH_INVALID);
				$result->error = 'Не верный логин(e-mail)/пароль!';
				return $result;
				/*if (Request::AJAX()) {
					return 'Не верный логин(e-mail)/пароль!';
				} else {
					Request::stop('Не верный логин(e-mail)/пароль!', Request::HH_ACCESSERROR, 'Ошибка при авторизации!');
				}*/
			}

			$row = DB::fetch($qr);
			
			if ($row['isbanned']) {
				Audit::add(Catalog::CLASS_ID_USER, self::$id, Audit::SAS_EV_AUTH, Audit::SAS_EV_AUTH_INVALIDBANNED);
				$result->error = 'Ваша учётная запись заблокирована ($login=" ' . $login . '")!';
				return $result;
				/*if (Request::AJAX()) {
					return 'Учётная запись с логином (e-mail\'ом) "'.$login.'" заблокирована!';
				} else {
					Request::stop('Учётная запись с логином (e-mail\'ом) "'.$login.'" заблокирована!', Request::HH_ACCESSERROR, 'Ошибка при авторизации!');
				}*/
			}
			if (! $row['status']) {
				Audit::add(Catalog::CLASS_ID_USER, self::$id, Audit::SAS_EV_AUTH, Audit::SAS_EV_AUTH_INVALIDSUSPENDED);
				$result->error = 'Ваша учётная запись деактивирована ($login=" ' . $login . '")!';
				return $result;
				/*if (Request::AJAX()) {
					return 'Учётная запись с логином (e-mail\'ом) "'.$login.'" деактивирована!';
				} else {
					Request::stop('Учётная запись с логином (e-mail\'ом) "'.$login.'" деактивирована!', Request::HH_ACCESSERROR, 'Ошибка при авторизации!');
				}*/
			}
			
			self::setValues( $row );

			$qr = DB::execute('UPDATE crs_session_history SET isbot = NULL WHERE (id = ?)', Session::getSessionID() );
			
			$params = Array(
				'timeout' => ConfigSession::sessionusertimeout,
				'user_id' => self::$id,
				'group_id' => self::$group_id,
				'isshowerror' => self::$isshowerror,
				'login' => self::$login,
				'fullname' => self::$fullname,
				'group_code' => self::$group_code,
				'email' => self::$email,
				'regdate' => self::$regdate,
				'isbot' => 0
			);
			
			$session_id = Session::createSession( $params );
			
			if (! $session_id) {
				return Request::stop('Ошибка в User::authorize(..)! Не удалось создать новую сессию', Request::HH_INTERNALERROR, 'Ошибка при авторизации!');
			}
			
			Audit::add(User::class_id_user, self::$id, Audit::SAS_EV_AUTH, Audit::SAS_EV_AUTH_LOGIN);
			$_SESSION['authorized'] = true;

			$result->success = true;
			return $result;
			
		} else {
			return Request::stop('Не верные параметры при вызове User::authorize(..)!', Request::HH_LOGICERROR, 'Ошибка при авторизации!');
		}
	}
	
	/**
	 * Выйти
	 */
	static public function logout() {
		
		if (! User::isAuthorized()) return false;
		
		$params = Array(
			'timeout' => ConfigSession::sessionguesttimeout,
			'user_id' => 0,
			'group_id' => 0,
			'isshowerror' => 0,
			'login' => 'guest',
			'fullname' => 'Guest',
			'group_code' => 'guests',
			'email' => null,
			'regdate' => null,
			'isbot' => 1 // На случай, если робот зайдёт через logout
		);
		
		$session_id = Session::createSession( $params );
		
		if (! $session_id) {
			return Request::stop('Ошибка в User::authorize(..)! Не удалось создать новую сессию', Request::HH_INTERNALERROR, 'Ошибка при авторизации!');
		}
		
		return true;
	}
	
	static public function id( $new_id = null) {
		if ($new_id) self::$id = $new_id;
		return self::$id;
	}
}

new User();
?>
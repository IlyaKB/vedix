<?php
namespace VediX;

function authenticate() {
	
	$response = new TObject();
	
	$token = Utils::_REQUEST('token');
	
	if ($token) {
	
		$uri = 'http://ulogin.ru/token.php?token=' . Utils::_REQUEST('token') . '&host=' . ConfigCatalog::get('sitedname');
		$s = file_get_contents($uri);
		$user = json_decode($s, true);

		if (! is_array($user)) {
			$response->error = 'Ошибка! Полученный ответ от социальной сети не распознан ($user = json_decode($s, true))!';
			return $response;
		}

		if (! empty($user['error'])) {
			$response->error = 'Ошибка: '.$user['error'] . Utils::rn . 'Попробуйте авторизоваться ещё раз!';
			return $response;
		}
		
		$social_network = $user['network'];
		$social_profile = $user['profile'];
		$login = $social_network . '-' . $user['uid'];
		$email = $user['email']; // Поменять можно потом в профиле
		$fullname = $user['first_name'] . ' ' . $user['last_name'];
		$pw = md5($login . '-' . $email . '-' . User::pw_suffix);

		$born_year = null;
		$born_month = null;
		$born_day = null;
		$borndate = Utils::multiExplode(Array('.', '-', '/'), Utils::getAElement($user, 'bdate'));
		if (isset($borndate[2])) {
			if (strlen($borndate[2]) == 4) {
				$born_year = $borndate[2];
				$born_month = $borndate[1];
				$born_day = $borndate[0];
			} else {
				$born_year = $borndate[0];
				$born_month = $borndate[1];
				$born_day = $borndate[2];
			}
		}

		$iswoman = null;
		if (isset($user['sex'])) {
			if ($user['sex'] == 1) $iswoman = 1; else if ($user['sex'] == 2) $iswoman = 0;
		}

		$country = Utils::getAElement($user, 'country');
		if (! $country) $country = null;

		$locality = Utils::getAElement($user, 'city');
		if (! $locality) $locality = null;

		$photo = Utils::getAElement($user, 'photo_big');
		if (! $photo) $photo = Utils::getAElement($user, 'photo');
		if (! $photo) $photo = null;

		$phone = Utils::getAElement($user, 'phone');
		if (! $phone) $phone = null;

		$qr = DB::execute('SELECT id FROM sec_user WHERE (login = ?) AND (pw = ?)', $login, $pw);
		$user_id = DB::fetch_val($qr);

		if (! $user_id) { // Регим юзера

			// Проверка на существование логина
			$id = DB::getValues('id', 'sec_user', 'login = ?', $login);
			if ($id) {
				$response->error = 'Пользователь с такими же учётными данными (логин/социальная сеть), но другим паролем уже зарегистрирован!';
				return $response;
			}

			$qr = DB::execute('INSERT INTO sec_user (
					group_id, login, fullname, email, pw, regdate, status, lastdate, social_network, social_profile,
					bornyear, bornmonth, bornday, iswoman, country, locality, photo, phone
				) VALUES (?,?,?,?,?,Now(),1,Now(),?,?,?,?,?,?,?,?,?, ?)',
				User::group_users, $login, $fullname, $email, $pw, $social_network, $social_profile,
				$born_year, $born_month, $born_day, $iswoman, $country, $locality, $photo, $phone);
			if (! $qr) {
				$response->error = 'Произошла ошибка при выполнении SQL-запроса к БД на добавление нового пользователя!';
				return $response;
			}
		}
	} else {
		$login = Utils::_REQUEST('user_login');
		$pw = Utils::_REQUEST('user_password');
	}
	
	$result = User::authorize( $login, $pw );
	return $result;
}
?>
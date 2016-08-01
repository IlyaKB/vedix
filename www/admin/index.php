<?php
namespace VediX;

header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: VediX');

date_default_timezone_set('Europe/Moscow');

ini_set('display_errors', 1); error_reporting(E_ALL); # Debug. TODO: off

//echo md5('surikat2014');
//echo md5('1');

include_once('../bootstrap.php');

include_once(HD_CORE . 'libs/mustache/src/Mustache/Autoloader.php');
\Mustache_Autoloader::register();
$mustache = new \Mustache_Engine(Array(
	'template_class_prefix' => 'mustache_',
	'cache' => MUSTACHE_CACHE_DIR,
	'loader' => new \Mustache_Loader_FilesystemLoader(HD_ROOT, Array('extension' => '.html')),
	'partials_loader' => new \Mustache_Loader_FilesystemLoader(HD_ROOT, Array('extension' => '.html'))
));

include_once('php/controller.php');


// Проверка авторизованности / logout
$response = new TObject();
if (! User::id()) {
	if (Request::AJAX()) {
		$jx = Request::JX();
		if ($jx != 'authenticate') {
			$response->error = 'Ошибка! Вы не авторизованы!';
		} else {
			include_once('../core/authenticate.php');
			$response = authenticate();
		}
		EXIT( json_encode($response) );
	} else {
		$model = new AdminModel();
		EXIT( $mustache->render('/admin/html/authorization', $model) );
	}
} else {
	if (Request::JX() == 'logout') {
		if (User::logout()) {
			$response->success = true;
		} else {
			$response->error = 'Error!';
		}
		EXIT( json_encode($response) );
	}
}

// Проверка доступа
if ( (! Request::AJAX()) && (User::$group_id != User::group_admins) && (User::$group_id != User::group_root) ) {
	$model = new AdminModel();
	EXIT( $mustache->render('/admin/html/accessforbidden', $model) );
}

$code = Request::KEY();

// Подключение контроллера раздела, если задан раздел
$controller = null;
if ($code) {
	$section = ConfigCatalog::getSectionAdmin($code);
	if (! $section) {
		RETURN Request::stop(
			'Раздел с кодом "' . $code . '" не найден!',
			Request::HH_INTERNALERROR,
			'Раздел с кодом "' . $code . '" не найден!'
		);
	}
	$section['code'] = $code;
	if (! isset($section['application'])) $section['application'] = 'applicationErrorOrIndexPage';
	if (! isset($section['type'])) $section['type'] = 'section';
	if (! isset($section['title'])) $section['title'] = 'Раздел без названия';
	$subdir = '';
	switch ($section['type']) {
		case 'module': $subdir = $section['application'] . '/' . $code . '/admin/'; break;
		case 'component': $subdir = $section['application'] . '/' . '_components/' . $code . '/admin/'; break;
		default: $subdir = '_admin/' . $code . '/';
	}
	$section['subdir'] = $subdir;
	$filename = HD_ROOT . 'catalog/' . $subdir . $code . '.admin.php';
	if (is_file($filename)) {
		include_once($filename);
		$className = 'VediX\\'.ucfirst($code) . 'Admin';
		if (class_exists($className)) {
			$controller = new $className($section);
		}
	}
	if (! $controller) {
		RETURN Request::stop(
			'Не найден класс раздела ($className = '.$className.')!',
			Request::HH_INTERNALERROR,
			'Не найден класс раздела!'
		);
		//$controller = new SectionDefaultController($section);
	}
} else {
	# NORM: admin index page #
}


// Обработка запроса
if (Request::AJAX()) {
	if ($controller) {
		$response = $controller->jxSwitch();
	} else {
		$response = new TObject();
		$response->error = 'Error! Controller of section is empty!';
	}
	EXIT( json_encode($response) );
} else {
	$model = new AdminModel();
	if ($controller) {
		$response = $controller->formOpen();
		$model->section = json_encode($response);
		$model->sectionJSFiles = ( isset($response->jsFiles) ? $response->jsFiles : Array() );
		$model->sectionCSSFiles = ( isset($response->cssFiles) ? $response->cssFiles : Array() );
		if (isset($response->jsFile)) {
			array_push($model->sectionJSFiles, $response->jsFile);
		}
		if (isset($response->cssFile)) {
			array_push($model->sectionCSSFiles, $response->cssFile);
		}
	}
	EXIT( $mustache->render('/admin/html/index', $model) );
}
?>
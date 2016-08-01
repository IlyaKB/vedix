<?php
namespace VediX;

require_once( HD_CORE . 'page.model.php');
require_once( HD_CORE . 'page.controller.php');

/**
 * Page
 * Класс для подготовки и отображения текущей страницы сайта. Запускает соответствующий контроллер страницы, подключает класс с данными страницы.
 * @author IlyaKB <veddbrus@mail.ru>
 * @version 1.0
 */
class Page {
	
	static private $instance;
	
	static private $controller = null;
	
	private $mustache;
	
	public function __construct() {
		
		if (! self::$instance) self::$instance = $this;
		
		if ( (Utils::_REQUEST('mustache')) || (! Request::AJAX()) ) { // Mustache не подключаем, когда используется AJAX (кроме случая с принудительным включением)
			include_once('core/libs/mustache/src/Mustache/Autoloader.php');
			\Mustache_Autoloader::register();
			$this->mustache = new \Mustache_Engine(Array(
				'template_class_prefix' => 'mustache_',
				'cache' => MUSTACHE_CACHE_DIR,
				'loader' => new \Mustache_Loader_FilesystemLoader(HD_ROOT, Array('extension' => '.html')),
				'partials_loader' => new \Mustache_Loader_FilesystemLoader(HD_ROOT, Array('extension' => '.html'))
				//'logger' => new Mustache_Logger_StreamLogger('php://stderr'),
				//',cache_file_mode' => 0666,
			));
		}
	}
	
	static public function getInstance() {
		if (! self::$instance) {
			self::$instance = new self ( );
		}
		return self::$instance;
	}
	
	/**
	 * Display page
	 * @return boolean
	 */
	public function display() {
		
		if ( (Request::AJAX()) && (! Utils::_REQUEST('mustache')) ) {
			Request::stop(
				'Ошибка! AJAX-запрос не был обработан соответствующим образом! Проверьте работу главного контроллера модуля.',
				Request::HH_INTERNALERROR,
				'Ошибка при обработка AJAX-запроса!'
			);
		}
		
		self::$controller->model->time = Request::getTime();
		
		if (! is_file(preg_replace('/[\/]?$/', '', HD_ROOT) . self::$controller->templateRender . '.html')) {
			Request::stop(
				'Ошибка! Отсутствует html-файл: ' . self::$controller->templateRender . '.html',
				Request::HH_INTERNALERROR,
				'Ошибка при открытии страницы!'
			);
		}
		echo $this->mustache->render(self::$controller->templateRender, self::$controller->model);
	}
	
	/**
	 * Подключить автоматически все модели и контроллеры и выполнить их
	 * Подключение начинается с контроллера/модели скина, затем веб-приложения, затем модуля (типа страниц)
	 */
	public function run() {
		
		$application = Request::APPLICATION();
		$module = Request::MODULE();
		
		$skin = ConfigCatalog::get('skin', $application, $module);
		
		$skin_dir = HD_ROOT . $skin . '/';
		define('HD_' . $skin, $skin_dir);
		$application_dir = $skin_dir . $application . '/';
		define('HD_' . strtoupper($application), $application_dir);
		$module_dir = $application_dir . $module . '/';
		define('HD_' . strtoupper($module), $module_dir);
		
		// Проверка директорий
		$this->checkDir($skin_dir, $skin, 'скина');
		$this->checkDir($application_dir, $application, 'веб-приложения');
		$this->checkDir($module_dir, $module, 'модуля');
		
		define('MUSTACHE_SKIN_DIR', '/' . $skin); // for FilesystemLoader.php (see in mustache dir)
		define('MUSTACHE_APPLICATION_DIR', '/' . $skin . '/' . $application); // for FilesystemLoader.php (see in mustache dir)

		// Подключаем контроллер/модель скина (0-уровень)
		$skin_main_file = $skin_dir . $skin . '.php';
		$this->checkFile($skin_main_file, $skin, 'скина', 'stop-error');
		include_once($skin_main_file);
		
		// Подключаем контроллер/модель веб-приложения (1-уровень)
		$application_main_file = $application_dir . $application . '.php';
		$this->checkFile($application_main_file, $application, 'веб-приложения', 'stop-error');
		include_once($application_main_file);
		
		// Определяем класс для запуска run()
		$classNameSkin = 'VediX\\'.ucfirst($skin) . 'Controller';
		$classNameApplication = 'VediX\\'.ucfirst($application) . 'Controller';
		$className = $classNameSkin; // по умолчанию класс скина
		
		// Подключаем контроллер/модуль модуля (2-уровень)
		$module_main_file = $module_dir . $module . '.php';
		if ($this->checkFile($module_main_file)) {
			include_once($module_main_file);
			$className = 'VediX\\'.ucfirst($module) . 'Controller';
		}
		
		$this->checkClass($className, $module, 'контроллера модуля');
		
		$params = Array('application' => $application, 'module' => $module, 'skin' => $skin);
		self::$controller = new $className( $params );
		
		// Подключение дефолтного css (скина или веб-приложения)
		if ($className == $classNameSkin) {
			self::$controller->model->resources_module = Array(
				Array('css' => 1, 'head' => 1, 'href' => '/'.$skin.'/_common/css/main.css')
			);
		} else if ($className == $classNameApplication) {
			self::$controller->model->resources_module = Array(
				Array('css' => 1, 'head' => 1, 'href' => '/'.$skin.'/'.$application.'/_common/css/main.css')
			);
		}
		
		// Автоматическое подключение ресурсов компонентов (js/css-файлов)
		// TODO:
		/*if ( (property_exists($controller->model, 'resources')) && ($controller->model->resources) ) {
			if (is_array($controller->model->resources)) {
				foreach ($controller->model->resources as $resource) {
					if ( (strpos($resource, '/') === false) && (strpos($resource, '\\') === false) ) {
						$resource = '/' . $skin . '/' . ($application ? $application . '/' : '') . '_components/' . $component . '/' . $resource;
					}
					$arr = (preg_match('/\.js$/i', $resource) ? Array('js' => 1, 'head' => 1, 'src' => $resource) : Array('css' => 1, 'head' => 1, 'href' => $resource) );
					array_push(self::$controller->model->resources_components, $arr);
				}
			}
			unset($controller->model->resources);
		}*/
		
		try {
			self::$controller->run();
			self::$controller->model->require['js_data'] = json_encode(self::$controller->model->jsData);
			
			// Подключение дефолтного js скина/веб-приложения
			if ($className == $classNameSkin) {
				self::$controller->model->require['page_script'] = $skin . '/' . $skin;
			} else if ($className == $classNameApplication) {
				self::$controller->model->require['page_script'] = $skin . '/' . $application . '/' . $application;
			}
		} catch ( Exception $e ) {
			Logger::append ( $e, 'Could not run component' );
		}
	}
	
	private function checkDir($dir, $what_code, $what_name) {
		if (is_dir($dir)) RETURN;
		EXIT(Request::stop(
			'Не найдена директория '.$what_name.'!'.Utils::rn.Utils::rn.
			'Код '.$what_name.': '.$what_code.Utils::rn.
			'Искомая директория: '.$dir,
			Request::HH_INTERNALERROR,
			'Не найдена директория '.$what_name.'!'));
	}
	
	private function checkFile($file, $what_code = '', $what_name = '', $flag = false) {
		if ( (! file_exists($file)) || (! is_file($file)) ) {
			if ($flag) {
				EXIT(Request::stop(
					'Не найден файл '.$what_name.'!'.Utils::rn.Utils::rn.
					'Код '.$what_name.': '.$what_code.Utils::rn.
					'Искомый файл: '.$file,
					Request::HH_INTERNALERROR,
					'Не найден файл '.$what_name.'!'));
			} else {
				RETURN FALSE;
			}
		} else {
			RETURN TRUE;
		}
	}
	
	private function checkClass($className, $what_code, $what_name) {
		if (! class_exists( $className )) {
			EXIT(Request::stop(
				'Класс '.$className.' не существует!'.Utils::rn.Utils::rn.
				'Код '.$what_name.': '.$what_code,
				Request::HH_INTERNALERROR,
				'Не найден класс '.$what_name.'!'));
		}
		RETURN TRUE;
	}
	
	/**
	 * Подключить компонент (не выполняя Run)
	 * @param string $component Код компонента
	 * @param string $application="site"
	 * @param string $skin=[значение_из_конфига]
	 * @param string $flag=null М.б. не задан, либо "global" (подключение глобального компонента)
	 * @return object Controller object
	 */
	static public function addComponent($component, $param = Array(), $application = DEFAULT_APPLICATION, $skin = null, $flag = null) {
		
		$controllerClassName = 'VediX\\'.ucfirst($component) . 'Controller';
		$modelClassName = 'VediX\\'.ucfirst($component) . 'Model';
		
		if (! $skin) {
			$skin = ConfigCatalog::get('skin', $application, $component);
			if (! $skin) $skin = DEFAULT_SKIN;
		}
		
		$cmp_dir = HD_ROOT . $skin . '/' . (! $flag && $application ? $application . '/' : '') . '_components/' . $component . '/';
		
		$cmp_main_file = $cmp_dir . $component . '.php';
		$cmp_controller_file = '';
		if (is_file($cmp_main_file)) {
			include_once($cmp_main_file);
		} else {
			$cmp_controller_file = $cmp_dir . $component . '.controller.php';
			if (! is_file($cmp_controller_file)) {
				RETURN Request::stop(
					'Файл '.$cmp_controller_file.' не существует!'.Utils::rn.Utils::rn.
					'Код компонента: '.$component.Utils::rn.
					($application ? 'Код веб-приложения: '.$application.Utils::rn : '').
					'Файл контроллера: '.$cmp_controller_file,
					Request::HH_INTERNALERROR,
					'Не найден файл контроллера компонента!'
				);
			}
			include_once($cmp_controller_file);
			
			$cmp_model_file = $cmp_dir . $component . '.model.php';
			if (is_file($cmp_model_file)) {
				include_once($cmp_model_file);
				if (! class_exists( $modelClassName )) {
					RETURN Request::stop(
						'Класс '.$modelClassName.' не существует!'.Utils::rn.Utils::rn.
						'Код компонента: '.$component.Utils::rn.
						($application ? 'Код веб-приложения: '.$application.Utils::rn : '').
						'Файл модели: '.$cmp_model_file,
						Request::HH_INTERNALERROR,
						'Не найден класс модели компонента!'
					);
				}
			}
		}
		
		if (! class_exists( $controllerClassName)) {
			RETURN Request::stop(
				'Класс '.$controllerClassName.' не существует!'.Utils::rn.Utils::rn.
				'Код компонента: '.$component.Utils::rn.
				($application ? 'Код веб-приложения: '.$application.Utils::rn : '').
				'Файл контроллера: '.($cmp_controller_file ? $cmp_controller_file : $cmp_main_file),
				Request::HH_INTERNALERROR,
				'Не найден класс контроллера компонента!'
			);
		}
		
		$controller = new $controllerClassName($param);
		
		if (! class_exists($modelClassName)) {
			$modelClassName = 'VediX\\'.'Model'; // На случай если в %component%.php нет класса модели
		}
		
		$controller->model = new $modelClassName();

		// Автоматическое подключение ресурсов компонента (js/css-файлов)
		/*if ( (property_exists($controller->model, 'resources')) && ($controller->model->resources) ) {
			if (is_array($controller->model->resources)) {
				foreach ($controller->model->resources as $resource) {
					if ( (strpos($resource, '/') === false) && (strpos($resource, '\\') === false) ) {
						$resource = '/' . $skin . '/' . ($application ? $application . '/' : '') . '_components/' . $component . '/' . $resource;
					}
					$arr = (preg_match('/\.js$/i', $resource) ? Array('js' => 1, 'head' => 1, 'src' => $resource) : Array('css' => 1, 'head' => 1, 'href' => $resource) );
					array_push(self::$controller->model->resources_components, $arr);
				}
			}
			unset($controller->model->resources);
		}*/
		
		return $controller;
	}
	
	/**
	 * Подключить глобальный компонент (не выполняя Run)
	 * @param string $component Код компонента
	 * @param string $application="site"
	 * @param string $skin=[значение_из_конфига]
	 * @param string $flag=null М.б. не задан, либо "global" (подключение глобального компонента)
	 * @return object Controller object
	 */
	static public function addGComponent($component, $param = Array(), $application = DEFAULT_APPLICATION, $skin = null) {
		return self::addComponent($component, $param, $application, $skin, 'global');
	}
	
	/**
	 * Подключить компонент и сразу же выполнить Run
	 * @param string $component Код компонента
	 * @param array $paramsCreate Параметры передаваемые в конструктор контроллера
	 * @param array $paramsRun Параметры передаваемые в метод run(..)
	 * @param string $application=DEFAULT_APPLICATION ('site')
	 * @param string $template=null
	 * @return object Controller object
	 */
	static public function addComponentAndRun($component, $paramsCreate = Array(), $paramsRun = Array(), $application = DEFAULT_APPLICATION, $skin = null) {
		
		$controller = self::addComponent($component, $paramsCreate, $application, $skin);
		
		if (! $controller) return FALSE;
		try {
			$controller->run($paramsRun);
		} catch ( Exception $e ) {
			Logger::append ( $e, 'Could not run component' );
		}
		return $controller;
	}
	
	/**
	 * Подключить глобальный компонент и сразу же выполнить Run
	 * @param string $gcomponent Код компонента
	 * @param array $paramsCreate Параметры передаваемые в конструктор контроллера
	 * @param array $paramsRun Параметры передаваемые в метод run(..)
	 * @param string $application=DEFAULT_APPLICATION ('site')
	 * @param string $template=null
	 * @return object Controller object
	 */
	static public function addGComponentAndRun($gcomponent, $paramsCreate = Array(), $paramsRun = Array(), $application = DEFAULT_APPLICATION, $skin = null) {
		
		$controller = self::addComponent($gcomponent, $paramsCreate, $application, $skin, 'global');
		
		if (! $controller) return FALSE;
		try {
			$controller->run($paramsRun);
		} catch ( Exception $e ) {
			Logger::append ( $e, 'Could not run gcomponent' );
		}
		return $controller;
	}
	
	/**
	 * Подключить модуль и при необходимости сразу же выполнить контроллер
	 * @param string $webapp Код веб-приложения
	 * @param string $appmod Код модуля
	 * @param Array/bool/NULL $params Если задан массив $params или значение true, то выполняется метод Controller::run($params) или просто Controller::run() соответственно
	 * @return object Указатель на контроллер. Для работы с данными контроллера используйте методы getData(..) и setData(..);
	 */
	/*static public function includeModule($webapp, $appmod, $params = null) {
		$file = HD_CATALOG . $webapp . '/' . $appmod . '/' . $appmod . '.controller.php';
		if ( (file_exists($file)) && (is_file($file)) ) {
			include_once($file);
			$className = 'VediX\\'.ucfirst($appmod) . 'Controller';
			if (!class_exists($className)) {
				return Request::stop('Не найден класс "'.$className.'"!', Request::HH_INTERNALERROR, 'Ошибка при подключении модуля!');
			}
			$controller = new $className( $webapp, $appmod );
			if (is_array($params)) {
				$result = $controller->run( $params );
			} else if ($params === true) {
				$result = $controller->run();
			}
			return $controller;
		} else {
			return Request::stop('Не найден файл контроллера для модуля "' . $appmod . '" веб-приложения "' . $webapp . '"!', Request::HH_INTERNALERROR, 'Ошибка при подключении модуля!');
		}
	}*/
}
?>
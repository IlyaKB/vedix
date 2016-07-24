<?php

/**
 * Base class for all other controller-class
 */
class PageController extends Controller {
	
	/**
	 * global $_PAGE
	 * @var &object Page
	 */
	protected $page = null;
	
	/**
	 * Текущий шаблон (skin)
	 * @var string
	 */
	protected $skin = null;
	
	/**
	 * Код веб-приоложения
	 * @var string
	 */
	protected $application = null;
	
	/**
	 * Код модуля
	 * @var string
	 */
	protected $module = null;

	/**
	 * Модель-данные, отправляемые в шаблонизатор
	 * @var &object Model
	 */
	public $model = null;
	
	/**
	 * Главный файл шаблон который надо вывести (путь относительно HD_ROOT)
	 * @var string
	 */
	public $templateRender = null;

	/**
	 * Instance (для случая, когда требуется контролировать, что бы создавался только один экземляр класса)
	 * @var object
	 */
	static protected $instance = null;
	static public function getInstance() {
		if (! self::$instance) {
			Request::stop('Ошибка! Нельзя вызвать getInstance() для класса контроллера, для которого не был сделан new!', Request::HH_INTERNALERROR, 'Недопустимый вызов функции!');
		}
		return self::$instance;
	}
	
	/**
	 * Корневой конструктор станицы с автоматическим подключением модели страницы (XxxModel)
	 * @param Array $params=Array('application' => 'site', 'module' => 'index', 'skin' => 'catalog', 'modelClassName' => null)
	 * @return void
	 */
	public function __construct( $params = Array('application' => 'site', 'module' => 'index', 'skin' => 'catalog', 'modelClassName' => null) ) {
		
		$this->application = Utils::getAElement($params, 'application', 'site');
		$this->module = Utils::getAElement($params, 'module', 'index');
		$this->skin = Utils::getAElement($params, 'skin', 'catalog');
		
		$modelClassName = Utils::getAElement($params, 'modelClassName', null);

		if (! self::$instance) self::$instance = $this;
		
		$this->page = Page::getInstance();
		
		$this->templateRender = '/' . $this->skin . '/' . $this->application . '/' . $this->module . '/' . $this->module;
		
		if (! $modelClassName) $modelClassName = ucfirst($this->module) . 'Model';
		#if (class_exists( $modelClassName )) $this->model = new $modelClassName(); // Модель лучше создавать явно в контроллерах, что бы IDE видела методы и поля класса
		if (! class_exists( $modelClassName )) {
			$modelClassName = ucfirst($this->application) . 'Model';
			if (class_exists( $modelClassName )) {
				$this->model = new $modelClassName();
			} else {
				RETURN Request::stop(
					'Для этой страницы сайта не найдена и не создана модель!'.Utils::rn.Utils::rn.
						'Модуль: '.$this->module.Utils::rn.
						'Приложение: '.$this->application.Utils::rn,
					Request::HH_INTERNALERROR,
					'Для этой страницы сайта не найдена и не создана модель!'
				);
			}
		}
	}
	
	public function run() {
		
		$model = $this->model;
		
		$model->developer_mode = Request::DEVELOPER_MODE();
		$model->is_production = ! Request::DEVELOPER_MODE();
		
		$model->demo = Request::DEMO();
		
		$model->class_name = ucfirst($this->module) . 'Model';
		
		$model->user = User::getArray();
		
		$model->config = Array(
			 'main' => ConfigCatalog::getSection('main'),
			 $this->application => ConfigCatalog::getSection($this->application, 'main'),
			 $this->module => ConfigCatalog::getSection($this->application, $this->module),
		);
		
		$model->request = Request::getArray(); // Параметры текущего запроса
		
		$model->page = Array(
			'web_src' => WEB_COMMON,
			'web_module' => WEB_CATALOG . $this->application . '/' . $this->module . '/',
			'web_data' => WEB_DATA
		);
		
		$model->require['page_script'] = preg_replace('/^\//', '', WEB_CATALOG) . $this->application . '/' . $this->module . '/' . $this->module;
		
		$model->h_title = (isset($model->config[$this->module]['title']) ? $model->config[$this->module]['title'] : null);
		$model->title = Utils::getAElement($model->config['main'], 'orgcode')
				. ($model->h_title ? Utils::getAElement($model->config['main'], 'title_sep') . $model->h_title : '');
		
		// Prepare base TS/JS Data
		
		$model->jsData->user = new TObject();
		$model->jsData->user->id = User::id();
		$model->jsData->user->login = User::$login;
		$model->jsData->user->fullname = User::$fullname;
		$model->jsData->user->email = User::$email;
		$model->jsData->user->phone = User::$phone;
		
		$model->jsData->entity = $model->entity;
	}
}
?>
<?php
namespace VediX;

class StaticModel extends SiteModel {
	public $page_code;
}

class StaticController extends SiteController {
	
	public function __construct( $params = Array('application' => null, 'module' => 'index', 'skin' => 'catalog') ) {
		
		$this->application = $params['application'];
		$this->module = $params['module'];
		$this->skin = $params['skin'];
		
		// render-файл и подключаем js/css-файлы статической страницы
	
		parent::__construct( $params );
		
		$this->model->page_code = Request::KEY();
		
		if ($this->model->page_code) {

			$dir = HD_STATIC . $this->model->page_code;
			if (is_dir($dir)) {
				
				$this->templateRender = '/' . $this->skin . '/' . $this->application . '/' . $this->module . '/' . $this->model->page_code . '/' . $this->model->page_code;
				$template = str_replace('//', '/', HD_ROOT . $this->templateRender . '.html');
			
				if (! is_file($template)) {
					RETURN Request::stop(
						'Для этой страницы не был найден шаблон!'.Utils::rn.Utils::rn.
							'Страница: '.$this->model->page_code.Utils::rn.
							'Модуль: '.$this->module.Utils::rn.
							'Приложение: '.$this->application.Utils::rn,
						Request::HH_INTERNALERROR,
						'Для этой страницы не был найден шаблон!'
					);
				}
				
				$this->model->resources_module = Array();
				
				/*$jsFile = $this->skin . '/'.$this->application.'/'.$this->module.'/'.$pagecode.'/'.$pagecode.'.js';
				if (is_file(HD_ROOT . $jsFile)) {
					array_push($this->model->resources_module, Array('js' => 1, 'head' => 1, 'src' => '/' . $jsFile));
				}*/				
				
				$cssFile = $this->skin . '/'.$this->application.'/'.$this->module.'/'.$this->model->page_code.'/'.$this->model->page_code.'.css';
				if (is_file(HD_ROOT . $cssFile)) {
					array_push($this->model->resources_module, Array('css' => 1, 'head' => 1, 'href' => '/' . $cssFile));
				}
			} else {
				RETURN Request::stop(
					'Для этой страницы не была найдена её директория!'.Utils::rn.Utils::rn.
						'Страница: '.$this->model->page_code.Utils::rn.
						'Модуль: '.$this->module.Utils::rn.
						'Приложение: '.$this->application.Utils::rn,
					Request::HH_INTERNALERROR,
					'Для этой страницы не была найдена её директория!'
				);
			}
		}
	}
	
	public function run() {
		
		parent::run();

		$this->model->require_page = preg_replace('/^\//', '', WEB_CATALOG) . $this->application . '/' . $this->module . '/' . $this->model->page_code . '/' . $this->model->page_code;
	}
}
?>
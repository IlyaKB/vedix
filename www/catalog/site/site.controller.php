<?php

/**
 * Главный контроллер веб-приложения "Сайт" (site)
 */
class SiteController extends CatalogController {
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
	}
	
	public function run() {
		
		parent::run();
		
		if (Request::AJAX()) {
			switch (Request::JX()) {
				case 'poll': EXIT($this->pollVote());
			}
		} else {
			
			// Главное меню
			$mmController = Page::addGComponentAndRun('mainmenu');
			$this->addData($mmController->model);
			
			// Левое вспомогательное меню
			$smController = Page::addComponentAndRun('sidemenu');
			$this->addData($smController->model);
			
			// Подключение конкретных опросов
			/*$pollController1 = Page::addComponentAndRun('poll', null, Array('id' => 1) );
			$this->model->poll_first = $pollController1->model->poll;
			$pollController2 = Page::addComponentAndRun('poll', null, Array('id' => 2) );
			$this->model->poll_second = $pollController2->model->poll;*/
			
			// Подключение одного блока опроса - выводится только не проголосованный опрос, а если все проголосованы, то случайно
			$pollController = Page::addComponentAndRun('poll');
			$this->model->poll_random = $pollController->model->poll;
		}
	}
	
	public function pollVote() {
		$resultController = Page::addComponent('poll');
		$result = $resultController->run();
		if ($resultController->model->success) {
		  clearCache('poll', Utils::_REQUEST('poll_id'));
		}
		return json_encode($resultController->model);
	}
}


/*$_PAGE = Page::getInstance();
		
		$hd_webapp_views = $this->getHDWebApp() . '_views/';
		// Пути к стандартным блокам, которые присутствуют во всех модулях веб-сайта
		$_PAGE->setData('page/templates', Array(
			'tpl_left' => $hd_webapp_views . 'left.tpl',
			'tpl_right' => $hd_webapp_views . 'right.tpl',
			'tpl_header' => $hd_webapp_views . 'header.tpl',
			'tpl_footer' => $hd_webapp_views . 'footer.tpl',
			'tpl_head' => $hd_webapp_views . 'head.tpl',
			'tpl_footer_widgets' => $hd_webapp_views . 'footer_widgets.tpl',
			'tpl_uagent_info' => $hd_webapp_views . 'blocks/uagent_info.tpl'
		));
		
		// Форма авторизации / профиль
		$controller = Page::includeModule('site', 'user', true);
		
		// Опросы
		$polls = Array(1, 2); // TODO: ИД выводимых опросов (выводим пока две штуки на странице)
		$controller = Page::includeModule('site', 'poll', Array( 'poll_id' => $polls[0]));
		$controller = Page::includeModule('site', 'poll', Array( 'poll_id' => $polls[1]));
		
		// Блок User-agent info
		$this->setData('uagent2', Array(
			'ispc' => (!Session::$mobile_detect->isMobile() && !Session::$mobile_detect->isMobile()),
			'ismobile' => Session::$mobile_detect->isMobile(),
			'istablet' => Session::$mobile_detect->isTablet(),
			'isandroid' => Session::$mobile_detect->is('AndroidOS'),
			'ipad_version' => Session::$mobile_detect->version('iPad'),
			'iphone_version' => Session::$mobile_detect->version('iPhone'),
			'android_version' => Session::$mobile_detect->version('Android'),
			'opera_mini_version' => Session::$mobile_detect->version('Opera Mini')
		));
		$this->setData('uagent3', Array(
			'istiertablet' => Session::$uagent_info->DetectTierTablet(), // These devices generally have larger screens (8 inches or larger) and their browsers are HTML 5-capable. These browsers handle CSS and JavaScript very well, which means that modest AJAX sites with native (iPad or other) style components typically work great. Includes: iPad, Android tablets (e.g., Xoom, Samsung Galaxy Tab 10.1), BlackBerry PlayBook, etc. // DetectTierRichCss
			'istieriphone' => Session::$uagent_info->DetectTierIphone(), // These are modern touchscreen  phones with WebKit browsers
			'istierphone' => Session::$uagent_info->DetectTierOtherPhones(), //Detects for all other mobile devices, with an emphasis on  feature phones. For these devices, it’s best to serve only the most basic styling, limited to little more than text color, alignment, and bold/italics. JavaScript support is virtually non-existent for these devices.  
			'isandroid' => Session::$uagent_info->DetectAndroid(),
			'isandroidtablet' => Session::$uagent_info->DetectAndroidTablet(),
			'isandroidphone' => Session::$uagent_info->DetectAndroidPhone(),
			'isipad' => Session::$uagent_info->DetectIpad(),
			'isipod' => Session::$uagent_info->DetectIpod(), // DetectIphoneOrIpod
			'isiphone' => Session::$uagent_info->DetectIphone(), // DetectIphoneOrIpod
			'issmartphone' => Session::$uagent_info->DetectSmartphone()
		));
		$this->setData('http_headers', apache_request_headers());
		
		if (User::$group_id) {
			$_PAGE->addHeadCSS(WEB_SRC . 'css/admin.css');
		}*/
?>
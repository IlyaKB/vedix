<?php
namespace VediX;

/**
 * Главный контроллер веб-приложения "Сайт" (site)
 */
class MagazinController extends CatalogController {
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
	}
	
	public function run() {
		
		parent::run();
		
		if (Request::AJAX()) {
			switch (Request::JX()) {
				// TODO
			}
		} else {
			// Главное меню
			$mmController = Page::addGComponentAndRun('mainmenu');
			$this->addData($mmController->model);
		}
	}
}
?>
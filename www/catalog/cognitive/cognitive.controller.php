<?php
namespace VediX;

/**
 * Главный контроллер веб-приложения "Cognitive" (cognitive)
 */
class CognitiveController extends CatalogController {
	
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
			// TODO
		}
	}
}
?>
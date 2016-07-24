<?php

/**
 * Главный контроллер веб-приложения "Etton" (etton)
 */
class EttonController extends CatalogController {
	
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
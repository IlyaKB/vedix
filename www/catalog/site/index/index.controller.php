<?php

class IndexController extends SiteController {
	
	public function __construct( $params = Array() ) {
		
		parent::__construct( $params );
		
		$this->model = new IndexModel();
	}
	
	public function run() {
		
		parent::run();
		
		return true;
	}
}
?>

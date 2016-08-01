<?php
namespace VediX;

class RedirectController extends SiteController {
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
	}
	
	public function run() {
		
		parent::run();
		
		$link = preg_replace('/\/redirect\//i', '', Utils::_SERVER('REQUEST_URI'));
		
		$this->model->link = $link;
		return TRUE;
	}
}

class RedirectModel extends SiteModel {
	
	public $link;
	
	public function __construct() {
		parent::__construct();
	}
}
?>
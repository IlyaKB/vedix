<?php

class IndexModel extends SiteModel {
	
	public $resources_module = Array(
		Array('css' => 1, 'head' => 1, 'href' => '/catalog/site/index/index.css')
	);
	
	public function __construct() {
		parent::__construct();
	}
}
?>
<?php
namespace VediX;

class PagesModel extends SiteModel {
	
	public $resources_module = Array(
		Array('css' => 1, 'head' => 1, 'href' => '/catalog/site/pages/pages.css')
	);
	
	public function __construct() {
		parent::__construct();
		
		$this->entity->type = 'page'; //$this->entity_type = 'page';
	}
}
?>
<?php
namespace VediX;

class SiteModel extends CatalogModel {
	
	public function __construct() {
		parent::__construct();
		
		$this->entity->type = 'site';
	}
}
?>
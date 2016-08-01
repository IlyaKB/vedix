<?php
namespace VediX;

class MagazinModel extends CatalogModel {
	
	public function __construct() {
		parent::__construct();
		
		$this->entity->type = 'magazin';
	}
}
?>
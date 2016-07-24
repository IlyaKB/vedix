<?php
class EttonModel extends CatalogModel {
	
	public function __construct() {
		parent::__construct();
		
		$this->entity->type = 'etton';
	}
}
?>
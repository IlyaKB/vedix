<?php
namespace VediX;

class CognitiveModel extends CatalogModel {
	
	public function __construct() {
		parent::__construct();
		
		$this->entity->type = 'cognitive';
	}
}
?>
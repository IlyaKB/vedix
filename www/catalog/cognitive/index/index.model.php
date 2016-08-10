<?php
namespace VediX;

/**
 * CognitiveModel
 */
final class IndexModel extends CognitiveModel {
	
	public $resources_module = Array(
		Array('css' => 1, 'head' => 1, 'href' => '/core/libs/jquery-ui-1.10.4/jquery-ui-1.10.4.custom.css'),
		Array('css' => 1, 'head' => 1, 'href' => '/catalog/cognitive/index/index.css')
	);
	
	public function __construct() {
		parent::__construct();
		$this->entity->type = 'index';
		$this->entity_start = true; // для подсветки активного пн.меню
		$this->cognitive = new TObject();
	}
}
?>
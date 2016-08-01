<?php
namespace VediX;

class CatalogModel extends PageModel {

	// TODO: сделать подключение ресурсов в ОДИН ОБЪЕКТ неким методом (а то не порядок...)
	public $resources_site = Array(
		Array('css' => 1, 'head' => 1, 'href' => '/core/libs/bootstrap-3.1.1/css/bootstrap.min.css')
	);
	
	public $currentDate = null;
	
	public function __construct() {
		parent::__construct();
		
		$this->entity->type = 'catalog';
		
		$this->currentDate = Utils::russianDate();
	}
}
?>
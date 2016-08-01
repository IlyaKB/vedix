<?php
namespace VediX;

/**
 * EttonModel
 */
final class IndexModel extends EttonModel {
	
	public $resources_module = Array(
		Array('css' => 1, 'head' => 1, 'href' => '/core/libs/jquery-ui-1.10.4/jquery-ui-1.10.4.custom.css'),
		Array('css' => 1, 'head' => 1, 'href' => '/catalog/etton/index/index.css')
	);
	
	public $magazin = null;
	
	public function __construct() {
		parent::__construct();
		$this->entity->type = 'index';
		$this->entity_start = true; // для подсветки активного пн.меню
		$this->etton = new TObject();
	}
}
?>
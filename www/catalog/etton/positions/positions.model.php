<?php
namespace VediX;

/**
 * Model for positions (каталог товаров)
 */
final class PositionsModel extends EttonModel {
	
	public $resources_module = Array(
		Array('css' => 1, 'head' => 1, 'href' => '/core/libs/jquery-ui-1.10.4/jquery-ui-1.10.4.custom.css'),
		Array('css' => 1, 'head' => 1, 'href' => '/catalog/etton/positions/positions.css')
	);
	
	public function __construct() {
		parent::__construct();
		
		$this->entity->type = 'position';
		
		$this->entity_positions = true;
	}
	
	/**
	 * Получить список товаров
	 * @return Array
	 */
	public function getList() {
		
		$items = Array();
		
		$qr = DB::execute('
			SELECT p.id, p.name, (SELECT count(*) FROM ett_positions_subtypes t WHERE (t.position_id = p.id)) AS subtypes_count
			FROM ett_positions p
			ORDER BY p.id');
		
		while ($item = DB::fetch_object($qr)) $items[] = $item;
			
		return $items;
	}
	
	/**
	 * Получить список подтипов товара
	 * @return Array
	 */
	public function getSubtypes($position_id) {
		
		$items = Array();
		
		$qr = DB::execute('
			SELECT t.id, t.name
			FROM ett_positions_subtypes t
			WHERE (t.position_id = ?)
			ORDER BY t.id;', $position_id);
		
		while ($item = DB::fetch_object($qr)) $items[] = $item;
			
		return $items;
	}
}
?>
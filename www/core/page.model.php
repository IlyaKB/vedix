<?php
namespace VediX;

/**
 * Модель для любой страницы сайта
 */
class PageModel extends Model {
	
	public $entity = null; // object
	public $class_name;
	
	public $developer_mode = false;
	public $is_production = true;
	
	public $demo = false;
	public $config;
	public $request;
	public $page;
	public $title;
	
	public $time;
	
	// Статическое подключение ресурсов (css)
	public $resources_base = Array();
	public $resources_site = Array();
	public $resources_module = Array();
	public $resources_components = Array();
	
	public $meta = Array(); // Meta tags
	public $jsData = null; // object (data for TS/JS)
	
	public $require = Array(
		'page_script' => null,
		'js_data' => '{}'
	);
	
	public function __construct() {
		$this->jsData = new TObject();
		$this->entity = new TObject();
		$this->entity->type = '';
		$this->entity->id = 0;
	}
}
?>
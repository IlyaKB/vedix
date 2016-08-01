<?php
namespace VediX;

class SidemenuController extends Controller {
	
	const TTL = 600;
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
	}
	
	public function run( $params = Array() ) {
		
		if (! getCache('sidemenu', null, $this->model->sidemenu)) {
			$this->model->sidemenu = $this->model->getChilds('sidemenu');
			setCache('sidemenu', null, $this->model->sidemenu, self::TTL);
		}
	}
}

class SidemenuModel extends Model {
	
	// Если ресурсы находятся там же что и контроллер компонента, то можно просто указывать имена файлов
	/*public $resources = Array(
		'sidemenu.css'
	);*/
	
	public $sidemenu = Array();
	
	public function __construct( $params = Array() ) {}
	
	public function getChilds( $parent_code = 'mainmenu', $parent_id = null ) {
		if (! $parent_id) {
			$parent_id = DB::getValues('id', 'web_menu', 'code = ?', $parent_code);
		}
		$items = Array();
		$qr = DB::execute('SELECT id, name, code, src, isredirection, rightbyif FROM web_menu WHERE (parent_id = ?) AND ( (IFNULL(isdemo,0) = 0) OR (isdemo = '.Request::DEMO().') ) ORDER BY number', $parent_id);
		while ($row = DB::fetch($qr)) {
			if ($row['isredirection']) $row['src'] = '/redirect/' . urlencode($row['src']);
			if ($row['code'] == Request::MODULE()) $row['active'] = 1;
			$row['childs'] = $this->getChilds( null, $row['id']);
			$row['childsEmpty'] = (isset($row['childs'][0]) ? false : true);
			$row['divider'] = ($row['code'] == 'divider' ? true : false);
			$items[] = $row;
		}
		return $items;
	}
}
?>
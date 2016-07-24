<?php

class MainmenuController extends Controller {
	
	const TTL = 1;//600;
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
	}
	
	public function run( $params = Array() ) {
		
		if (! getCache('mainmenu', null, $this->model->mainmenu)) {
			$this->model->mainmenu = $this->getChilds('mainmenu');
			setCache('mainmenu', null, $this->model->mainmenu, self::TTL);
		}
		
		$url = preg_replace('/\?.*/i', '', Utils::_SERVER('REQUEST_URI'));
		$this->checkCurrentItems($this->model->mainmenu, $url);
	}
	
	private function getChilds( $parent_code = 'mainmenu', $parent_id = null ) {
		if (! $parent_id) {
			$parent_id = DB::getValues('id', 'web_menu', 'code = ?', $parent_code);
		}
		$items = Array();
		$qr = DB::execute('SELECT id, name, code, src, isredirection, rightbyif, regexp_current FROM web_menu WHERE (parent_id = ?) AND (isnotinuse = 0) AND ( (IFNULL(isdemo,0) = 0) OR (isdemo = '.Request::DEMO().') ) ORDER BY number', $parent_id);
		while ($row = DB::fetch_object($qr)) {
			if ($row->isredirection) $row->src = '/redirect/' . urlencode($row->src);
			if (! $row->src) {
				$row->src = '#';
				$row->hrefEmpty = true;
			} else {
				$row->hrefEmpty = false;
			}
			if ($row->code == Request::MODULE()) $row->active = 1;
			$row->childs = $this->getChilds( null, $row->id);
			$row->childsEmpty = (isset($row->childs[0]) ? false : true);
			$row->divider = ($row->code == 'divider' ? true : false);
			$items[] = $row;
		}
		return $items;
	}
	
	private function checkCurrentItems(&$items, $url) {
		$isDetect = 0;
		foreach ($items as &$item) {
			if ($item->regexp_current) {
				if (preg_match($item->regexp_current, $url)) {
					$item->current = 1;
					$isDetect = 1;
					break;
				}
			}
			$isDetect = $this->checkCurrentItems($item->childs, $url);
		}
		return $isDetect;
	}
}

class MainmenuModel extends Model {
	
	// Если ресурсы находятся там же что и контроллер компонента, то можно просто указывать имена файлов
	/*public $resources = Array(
		'mainmenu.css'
	);*/
	
	public $mainmenu = Array();
}
?>
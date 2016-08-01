<?php
namespace VediX;

include_once('/../../_common/php/common_utils.php'); // CommonUtils::
include_once('php/detail.php'); // MagazinDetail::

class IndexController extends MagazinController {
	
	const TTL = 1;
	const CELL_COUNT = 12;
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
		
		$this->model = new IndexModel();
	}
	
	public function run() {
		
		parent::run();
		
		$category_url_name = Request::KEY();
		if (! $category_url_name) $category_url_name = '';
		
		$position_url_name = Request::KEY_FEATURE();
		
		if (Request::AJAX()) {
			switch (Request::JX()) {
				//case 'visit': EXIT(WebUtils::visit('blog', 'web_blog_posts', $post_url_name));
				//case 'voting': EXIT($this->voting($post_url_name));
				case 'comments': EXIT($this->commentsRun());
				default: {
					Request::stop('Получена не поддерживаемая команда ('.Request::JX().')!', Request::HH_ACCESSERROR);
				}
			}
		} else {
			
			$model = $this->model;
			$magazin = $model->magazin;
			
			// Категории
			if (! getCache('magazin_categories', null, $magazin->categories)) {
				$magazin->categories = $this->getCategories();
				setCache('magazin_categories', null, $magazin->categories, self::TTL);
			}
			
			$this->setCurrentCategory($magazin->categories, $category_url_name);
			
			$magazin->category = null;
			
			if ($category_url_name) {
				$magazin->category = new TObject();
				$magazin->category->url_name = $category_url_name;
				list($magazin->category->id, $magazin->category->name) = $this->getCategoryIdName($magazin->categories, $category_url_name);
			}
			
			$jsData = $model->jsData;
		
			if (! $position_url_name) {
				
				$jsData->is_list = true;

				// список товаров и услуг
				$page_num = (int)Utils::_REQUEST('p');
				if (! $page_num) $page_num = 1;
				
				$params = Array('category_id' => ($magazin->category ? $magazin->category->id : null), 'page_num' => $page_num);
				
				if (! getCache('magazin_list', $params, $magazin->list)) {
					$magazin->list = $this->getList($magazin->category, $page_num);
					setCache('magazin_list', $params, $magazin->list, self::TTL);
				}
				
				$jsData->filters = $magazin->list->filters;
				
				$jsData->manufacturers = Array();
				if (! getCache('magazin_manufacturers', null, $jsData->manufacturers)) {
					$jsData->manufacturers = $this->getManufacturers();
					setCache('magazin_manufacturers', null, $jsData->manufacturers, self::TTL);
				}
				
			} else {
				
				$jsData->is_record = true;
				$jsData->entity = new TObject();
				$jsData->detail = new TObject();

				if (! getCache('magazin_detail', $position_url_name, $magazin->detail)) {
					$magazin->detail = MagazinDetail::get($position_url_name);
					setCache('magazin_detail', $position_url_name, $magazin->detail, self::TTL);
				}

				$detail = $magazin->detail;

				// Comments
				if (! getCache('comments_magposition', $position_url_name, $model->comments)) {
					$commentsController = Page::addGComponent('comments');
					$commentsController->run( Array('entity_type' => 'magposition', 'entity_id' => $detail->id) );
					$model->comments = $commentsController->model;
					setCache('comments_magposition', $position_url_name, $model->comments, self::TTL);
				}

				$model->title = Utils::getAElement($model->config['main'], 'orgcode') . Utils::getAElement($model->config['main'], 'title_sep') . $detail->name;

				// Meta tags
				$model->meta[] = Array('property' => 'og:title', 'content' => str_replace('"', '&quot;', $detail->name));
				$model->meta[] = Array('property' => 'og:description', 'content' => str_replace('"', '&quot;', $detail->name));
				//TODO: $this->model->meta[] = Array('property' => 'og:image', 'content' => $this->model->detail->image);

				$entity = $jsData->entity;
				$entity->type = $this->model->entity->type;
				$entity->id = $detail->id;

				$datadetail = $jsData->detail;
				$datadetail->hits = $detail->hits;
				$datadetail->votes = $detail->votes;
				$datadetail->votesCount = $detail->votes_count;

				// hits
				$positionHits = Utils::_SESSION('magposition_hits');
				if (! $positionHits) $positionHits = Array();
				$datadetail->viewed = (in_array($detail->id, $positionHits) ? 1 : 0);
			}
		}

		return TRUE;
	}
	
	private function getCategoryIdName(&$categories, $url_name) {
		if (! $url_name) return Array(null, null);
		foreach ($categories as &$category) {
			if ($category->url_name != $url_name) continue;
			return Array($category->id, $category->name);
		}
		return Array(null, null);
	}
	
	private function getCategories() {
		$items = Array();
		$sql = 'SELECT c.id, c.url_name, c.name
				FROM mag_categories c
				WHERE (c.parent_id IS NOT NULL) AND (EXISTS(SELECT id FROM mag_positions p WHERE (p.category_id = c.id) AND (p.status = 1)))
				ORDER BY c.name ASC';
		$qr = DB::execute($sql);
		while ($category = DB::fetch_object($qr)) {
			$items[] = $category;
		}
		return $items;
	}
	
	private function setCurrentCategory($categories, $category_url_name) {
		if (! $category_url_name) return;
		foreach ($categories as &$category) {
			if ($category->url_name == $category_url_name) {
				$category->is_current = 1;
				break;
			}
		}
	}
	
	private function getList($category = null, $page_num = 1) {
		
		$items = Array();
		$index = 1;
		$start = ($page_num - 1) * self::CELL_COUNT;
		$params = Array();
		if ($category) {
			$params[] = $category->id;
		}
		$sql = 'SELECT SQL_CALC_FOUND_ROWS r.id, r.name, r.url_name, r.photo_url, c.url_name AS category_url_name,
					r.is_alloworder, r.is_available, r.quantity, m.name_short measure_name, r.price
				FROM mag_positions r
					LEFT JOIN mag_categories c ON (c.id = r.category_id)
					LEFT JOIN mag_measures m ON (m.id = r.measure_id)
				WHERE (r.status = 1) '.($category ? ' AND (r.category_id = ?) ' : '').'
				ORDER BY r.number DESC LIMIT ' . $start . ', '.self::CELL_COUNT.';';
		$qr = DB::execute($sql, $params);
		while ($item = DB::fetch_object($qr)) {
			$item->index = $index++;
			$item->name = htmlentities($item->name);
			if (! is_file(HD_ROOT . $item->photo_url)) {
				$item->photo_url = '/data/magazin/photo_no.png';
			}
			$items[] = $item;
		}
		
		$total = DB::getValues('FOUND_ROWS() total');
		
		$list = new TObject();
		$list->items = $items;
		$list->pagination = CommonUtils::paginationBuild('magazin' . ($category ? '/index/' . $category->url_name : ''), $page_num, $total, self::CELL_COUNT);
		
		$list->filters = new TObject();
		
		if ($row = DB::fetch_object( DB::execute('SELECT MIN(r.price) minmin, MAX(r.price) maxmax FROM mag_positions r WHERE (r.status = 1)') ) ) {
			$list->filters->priceMinMin = floor($row->minmin / 100) * 100;
			$list->filters->priceMaxMax = ceil($row->maxmax / 100) * 100;
		}
		
		return $list;
	}
	
	private function getManufacturers() {
		$items = Array();
		$qr = DB::execute('SELECT url_name, name FROM mag_manufacturers ORDER BY id');
		while ($item = DB::fetch_object($qr)) {
			$item->name = htmlentities($item->name);
			$items[] = $item;
		}
		return $items;
	}
	
	/**
	 * Учесть голос (оценку)
	 */
	public function voting($url_name) {
		$resultController = Page::addComponentAndRun('voting');
		if ($resultController->model->success) {
		  clearCache('magposition_detail', $url_name);
		}
		return json_encode($resultController->model);
	}
	
	/**
	 * Добавить коммент
	 */
	public function commentsRun() {
		$resultController = Page::addGComponent('comments');
		$result = $resultController->run( Array('entity_type' => 'magposition', 'entity_id' => Utils::_REQUEST('entity_id')) );
		if ($resultController->model->success) {
		  clearCache('magposition', $pagecode);
		}
		return json_encode($resultController->model);
	}
}

class IndexModel extends MagazinModel {
	
	public $resources_module = Array(
		Array('css' => 1, 'head' => 1, 'href' => '/core/libs/jquery-ui-1.10.4/jquery-ui-1.10.4.custom.css'),
		Array('css' => 1, 'head' => 1, 'href' => '/catalog/magazin/index/index.css')
	);
	
	public $magazin = null;
	
	public function __construct() {
		parent::__construct();
		$this->entity->type = 'magazin';
		$this->magazin = new TObject();
		$this->magazin->categories = null;
		$this->magazin->list = null;
		$this->magazin->detail = null;
	}
}
?>
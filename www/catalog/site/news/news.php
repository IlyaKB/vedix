<?php
namespace VediX;

include_once('/../../_common/php/common_utils.php');
include_once('/../_common/php/web_utils.php');

class NewsController extends SiteController {
	
	const TTL = 600;
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
		
		$this->model = new NewsModel();
	}
	
	public function run() {
		
		parent::run();
		
		$url_name = Request::KEY();
		
		if (Request::AJAX()) {
			switch (Request::JX()) {
				case 'visit': EXIT(WebUtils::visit('news', 'web_news', $url_name));
				case 'voting': EXIT($this->voting($url_name));
				case 'comments': EXIT($this->commentsRun());
				default: {
					Request::stop('Получена не поддерживаемая команда ('.Request::JX().')!', Request::HH_ACCESSERROR);
				}
			}
		} else {
		
			if (! $url_name) {
				$page_num = (int)Utils::_REQUEST('p');
				if (! $page_num) $page_num = 1;
				if (! getCache('news_list', $page_num, $this->model->list)) {
					$this->model->list = $this->getList($page_num);
					setCache('news_list', $page_num, $this->model->list, self::TTL);
				}
				$data = $this->model->jsData;
				$data->is_list = true;
			} else {

				$model = $this->model;

				$data = $model->jsData;
				$data->is_record = true;
				$data->entity = new TObject();
				$data->detail = new TObject();

				if (! getCache('news_detail', $url_name, $model->detail)) {
					$model->detail = $this->getDetail($url_name);
					setCache('news_detail', $url_name, $model->detail, self::TTL);
				}

				$detail = $model->detail;

				// Comments
				if (! getCache('comments_news', $url_name, $model->comments)) {
					$commentsController = Page::addGComponent('comments');
					$commentsController->run( Array('entity_type' => 'news', 'entity_id' => $detail->id) );
					$model->comments = $commentsController->model;
					setCache('comments_news', $url_name, $model->comments, self::TTL);
				}

				$model->title = Utils::getAElement($model->config['main'], 'orgcode') . Utils::getAElement($model->config['main'], 'title_sep') . $detail->caption;

				// Meta tags
				$model->meta[] = Array('property' => 'og:title', 'content' => str_replace('"', '&quot;', $detail->caption));
				$model->meta[] = Array('property' => 'og:description', 'content' => str_replace('"', '&quot;', $detail->announce));
				//TODO: $this->model->meta[] = Array('property' => 'og:image', 'content' => $this->model->detail->image);

				$entity = $data->entity;
				$entity->type = $this->model->entity->type; //$model->entity_type;
				$entity->id = $detail->id;

				$datadetail = $data->detail;
				$datadetail->hits = $detail->hits;
				$datadetail->votes = $detail->votes;
				$datadetail->votesCount = $detail->votes_count;

				// hits
				$newsHits = Utils::_SESSION('news_hits');
				if (! $newsHits) $newsHits = Array();
				$datadetail->viewed = (in_array($detail->id, $newsHits) ? 1 : 0);
			}
		}

		return TRUE;
	}
	
	private function getList($page_num = 1) {
		
		$items = Array();
		$index = 1;
		$start = ($page_num - 1) * 10;
		$qr = DB::execute('SELECT SQL_CALC_FOUND_ROWS n.id, n.docdate, n.caption, n.url_name, n.announce, n.hits, n.ahits, n.srcinfo, n.votes,
					n.author_id, IFNULL(u.fullname, u.login) author_name,
					(SELECT COUNT(*) FROM web_comments c WHERE (c.entity_type = "news") AND (c.entity_id = n.id)) AS comments_count
				FROM web_news n
					LEFT JOIN sec_user u ON (u.id = n.author_id)
				WHERE (n.status = 1) ORDER BY n.docdate DESC LIMIT ' . $start . ', 10;');
		while ($item = DB::fetch_object($qr)) {
			$item->index = $index++;
			WebUtils::prepareSrcInfo($item);
			$item->docdate = Utils::getDateStr($item->docdate);
			$items[] = $item;
		}
		
		$total = DB::getValues('FOUND_ROWS() total');
		$pagination = CommonUtils::paginationBuild('news', $page_num, $total);
		
		return Array('items' => $items, 'pagination' => $pagination);
	}
	
	public function getDetail( $key ) {
		
		$qr = DB::execute('
			SELECT
				d.id, d.docdate, d.upddate, d.body, d.caption, d.url_name, d.author_id, d.hits, d.tags, d.announce,
				d.srcinfo, d.isallowvote, d.isallowcomments, d.votes, d.votes_count, IFNULL(u.fullname, u.login) author_name,
				(SELECT COUNT(*) FROM web_comments c WHERE (c.entity_type = "news") AND (c.entity_id = d.id)) AS comments_count
			FROM web_news d
				LEFT JOIN sec_user u ON (u.id = d.author_id)
			WHERE (d.url_name = ?)
			ORDER BY d.docdate desc',
			$key
		);
		
		if (! ($detail = DB::fetch_object($qr))) return Request::stop('Запрашиваемая новость не найдена!', Request::HH_NOTFOUNDED, 'Запрашиваемая новость не найдена!');
		
		$detail->docdate = Utils::getDateStr($detail->docdate);
		$detail->upddate = preg_replace('/:\d\d$/', '', $detail->upddate);
				
		// hits
		$detail->hitsPrint = ($detail->hits < 10 ? 'менее 10' : $detail->hits);
		
		// votes
		CommonUtils::prepareVotesData($detail);
		
		$detail->author = null;
		if ($detail->author_name) {
			$detail->author = Array(
				'name' => $detail->author_name,
				'id' => $detail->author_id
			);
		}
		
		WebUtils::prepareTags($detail);
		WebUtils::prepareSrcInfo($detail);
		
		return $detail;
	}
	
	/**
	 * Учесть голос (оценку)
	 */
	public function voting($pagecode) {
		$resultController = Page::addComponentAndRun('voting');
		if ($resultController->model->success) {
		  clearCache('news_detail', $pagecode);
		}
		return json_encode($resultController->model);
	}
	
	/**
	 * Добавить коммент
	 */
	public function commentsRun() {
		$resultController = Page::addGComponent('comments');
		$resultController->run( Array('entity_type' => 'news', 'entity_id' => Utils::_REQUEST('entity_id')) );
		if ($resultController->model->success) {
		  clearCache('news', $pagecode);
		}
		return json_encode($resultController->model);
	}
}

class NewsModel extends SiteModel {
	
	public $resources_module = Array(
		Array('css' => 1, 'head' => 1, 'href' => '/catalog/site/news/news.css')
	);
	
	public $lis;
	public $detail;
	
	public function __construct() {
		parent::__construct();
		$this->entity->type = 'news'; //$this->entity_type = 'news';
		$this->list = null;
		$this->detail = null;
	}
}
?>
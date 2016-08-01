<?php
namespace VediX;

include_once('/../../_common/php/common_utils.php');
include_once('/../_common/php/web_utils.php');

class BlogController extends SiteController {
	
	const TTL = 600;
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
		
		$this->model = new BlogModel();
	}
	
	public function run() {
		
		parent::run();
		
		$category_url_name = Request::KEY();
		if (! $category_url_name) $category_url_name = '';
		
		$post_url_name = Request::KEY_FEATURE();
		
		if (Request::AJAX()) {
			switch (Request::JX()) {
				case 'visit': EXIT(WebUtils::visit('blog', 'web_blog_posts', $post_url_name));
				case 'voting': EXIT($this->voting($post_url_name));
				case 'comments': EXIT($this->commentsRun());
				default: {
					Request::stop('Получена не поддерживаемая команда ('.Request::JX().')!', Request::HH_ACCESSERROR);
				}
			}
		} else {
			
			$model = $this->model;
			$blog = $model->blog;
			
			// список категорий
			if (! getCache('blog_categories', null, $blog->categories)) {
				$blog->categories = $this->getCategories();
				setCache('blog_categories', null, $blog->categories, self::TTL);
			}
			
			$this->setCurrentCategoryAndPost($blog->categories, $category_url_name, $post_url_name);
			
			$category_id = null;
			
			if ($category_url_name) {
				list($category_id, $category_name) = $this->getCategoryIdName($blog->categories, $category_url_name);
				$blog->category = new TObject();
				$blog->category->url_name = $category_url_name;
				$blog->category->name = $category_name;
			}
		
			if (! $post_url_name) {

				// список постов
				$page_num = (int)Utils::_REQUEST('p');
				if (! $page_num) $page_num = 1;
				
				$params = Array('category_id' => $category_id, 'page_num' => $page_num);
				
				if (! getCache('blog_list', $params, $blog->list)) {
					$blog->list = $this->getList($category_id, $page_num);
					setCache('blog_list', $params, $blog->list, self::TTL);
				}
				
				$data = $model->jsData;
				$data->is_list = true;
				
			} else {

				$data = $model->jsData;
				$data->is_record = true;
				$data->entity = new TObject();
				$data->detail = new TObject();

				if (! getCache('blog_detail', $post_url_name, $blog->detail)) {
					$blog->detail = $this->getDetail($post_url_name);
					setCache('blog_detail', $post_url_name, $blog->detail, self::TTL);
				}

				$detail = $blog->detail;

				// Comments
				if (! getCache('comments_blog', $post_url_name, $model->comments)) {
					$commentsController = Page::addGComponent('comments');
					$commentsController->run( Array('entity_type' => 'blog', 'entity_id' => $detail->id) );
					$model->comments = $commentsController->model;
					setCache('comments_blog', $post_url_name, $model->comments, self::TTL);
				}

				$model->title = Utils::getAElement($model->config['main'], 'orgcode') . Utils::getAElement($model->config['main'], 'title_sep') . $detail->caption;

				// Meta tags
				$model->meta[] = Array('property' => 'og:title', 'content' => str_replace('"', '&quot;', $detail->caption));
				$model->meta[] = Array('property' => 'og:description', 'content' => str_replace('"', '&quot;', $detail->metadesc));
				//TODO: $this->model->meta[] = Array('property' => 'og:image', 'content' => $this->model->detail->image);

				$entity = $data->entity;
				$entity->type = $this->model->entity->type;
				$entity->id = $detail->id;

				$datadetail = $data->detail;
				$datadetail->hits = $detail->hits;
				$datadetail->votes = $detail->votes;
				$datadetail->votesCount = $detail->votes_count;

				// hits
				$blogHits = Utils::_SESSION('blog_hits');
				if (! $blogHits) $blogHits = Array();
				$datadetail->viewed = (in_array($detail->id, $blogHits) ? 1 : 0);
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
		
		$sql_posts = 'SELECT p.id, p.url_name, p.smallcaption
				FROM web_blog_posts p
				WHERE (p.category_id = ?)
				ORDER BY p.docdate DESC
				LIMIT 0, 10';
		$sql = 'SELECT c.id, c.url_name, c.name
				FROM web_blog_categories c
				ORDER BY c.name ASC';
		
		$qr = DB::execute($sql);
		
		while ($category = DB::fetch_object($qr)) {
			$qr_posts = DB::execute($sql_posts, $category->id);
			$category->posts = Array();
			$qPosts = 0;
			while ($post = DB::fetch_object($qr_posts)) {
				$qPosts++;
				$post->category_url_name = $category->url_name;
				$category->posts[] = $post;
			}
			if (! $qPosts) continue;
			if ($qPosts) {
				$category->posts[$qPosts - 1]->is_last = 1;
			}
			$items[] = $category;
		}
		
		return $items;
	}
	
	private function setCurrentCategoryAndPost($categories, $category_url_name, $post_url_name) {
		$qCategories = 0;
		foreach ($categories as &$category) {
			$qCategories++;
			if ( (! $category_url_name) && ($qCategories == 1) ) {
				$category->is_expand = 1;
			}

			foreach ($category->posts as &$post) {
				if ($post->url_name == $post_url_name) {
					$post->is_current = 1;
				} else {
					$post->is_current = 0; // особенности php-mustache в части вложенных условий (если переменная undefined, то она берется с уровня выше)
				}
			}
			
			if ($category->url_name == $category_url_name) {
				$category->is_expand = 1;
				$category->is_current = 1;
				break;
			}
		}
	}
	
	private function getList($category_id = null, $page_num = 1) {
		
		$items = Array();
		$index = 1;
		$start = ($page_num - 1) * 10;
		$params = Array();
		if ($category_id) {
			$params[] = $category_id;
		}
		$sql = 'SELECT SQL_CALC_FOUND_ROWS r.id, r.docdate, r.caption, r.url_name, r.announce, r.hits, r.ahits, r.srcinfo, r.votes,
					r.author_id, IFNULL(u.fullname, u.login) author_name,
					(SELECT COUNT(*) FROM web_comments c WHERE (c.entity_type = "blog") AND (c.entity_id = r.id)) AS comments_count,
					c.url_name AS category_url_name
				FROM web_blog_posts r
					LEFT JOIN sec_user u ON (u.id = r.author_id)
					LEFT JOIN web_blog_categories c ON (c.id = r.category_id)
				WHERE (r.status = 1) '.($category_id ? ' AND (r.category_id = ?) ' : '').'
				ORDER BY r.docdate DESC LIMIT ' . $start . ', 10;';
		$qr = DB::execute($sql, $params);
		while ($item = DB::fetch_object($qr)) {
			$item->index = $index++;			
			WebUtils::prepareSrcInfo($item);
			$item->docdate = Utils::getDateStr($item->docdate);
			$items[] = $item;
		}
		
		$total = DB::getValues('FOUND_ROWS() total');
		
		$list = new TObject();
		$list->items = $items;
		$list->pagination = CommonUtils::paginationBuild('blog', $page_num, $total);
		
		return $list;
	}
	
	public function getDetail( $key ) {
		
		$qr = DB::execute('
			SELECT
				d.id, d.docdate, d.upddate, d.body, d.caption, d.url_name, d.author_id, d.hits, d.tags, d.metadesc,
				d.srcinfo, d.isallowvote, d.isallowcomments, d.votes, d.votes_count, IFNULL(u.fullname, u.login) author_name,
				(SELECT COUNT(*) FROM web_comments c WHERE (c.entity_type = "blog") AND (c.entity_id = d.id)) AS comments_count,
				c.url_name AS category_url_name, c.name AS category_name
			FROM web_blog_posts d
				LEFT JOIN web_blog_categories c ON (c.id = d.category_id)
				LEFT JOIN sec_user u ON (u.id = d.author_id)
			WHERE (d.url_name = ?)', $key);
		
		if (! ($detail = DB::fetch_object($qr))) return Request::stop('Запрашиваемый пост не найден!', Request::HH_NOTFOUNDED, 'Запрашиваемый пост не найден!');
		
		if (Request::DEVELOPER_MODE()) {
			$detail->body = preg_replace('/http[s]?:\/\/.*[\/]?/i', ConfigSite::siteprotocol . '://' . ConfigSite::$sitedname . '/#DEVELOPER_MODE', $detail->body);
		}
		
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
	public function voting($url_name) {
		$resultController = Page::addComponentAndRun('voting');
		if ($resultController->model->success) {
		  clearCache('blog_detail', $url_name);
		}
		return json_encode($resultController->model);
	}
	
	/**
	 * Добавить коммент
	 */
	public function commentsRun() {
		$resultController = Page::addGComponent('comments');
		$result = $resultController->run( Array('entity_type' => 'blog', 'entity_id' => Utils::_REQUEST('entity_id')) );
		if ($resultController->model->success) {
		  clearCache('blog', $pagecode);
		}
		return json_encode($resultController->model);
	}
}

class BlogModel extends SiteModel {
	
	public $resources_module = Array(
		Array('css' => 1, 'head' => 1, 'href' => '/catalog/site/blog/blog.css')
	);
	
	public $blog = null;
	
	public function __construct() {
		parent::__construct();
		$this->entity->type = 'blog'; //$this->entity_type = 'blog';
		$this->blog = new TObject();
		$this->blog->categories = null;
		$this->blog->list = null;
		$this->blog->detail = null;
	}
}
?>
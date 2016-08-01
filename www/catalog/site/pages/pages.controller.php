<?php
namespace VediX;

include_once('/../../_common/php/common_utils.php');
include_once('/../_common/php/web_utils.php');

class PagesController extends SiteController {
	
	const TTL = 600;
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
		
		$this->model = new PagesModel();
	}
	
	public function run() {
		
		parent::run();
	
		$url_name = Request::KEY();
		
		if (Request::AJAX()) {
			switch (Request::JX()) {
				case 'visit': EXIT(WebUtils::visit('page', 'web_pages', $url_name));
				case 'voting': EXIT($this->voting($url_name));
				case 'comments': EXIT($this->commentsRun());
				default: {
					Request::stop('Получена не поддерживаемая команда ('.Request::JX().')!', Request::HH_ACCESSERROR);
				}
			}
		} else {
		
			parent::run();

			if ($url_name) {
				
				/*// TODO права: if (! $this->model->check_rights(WebDocumentModel::DT_PAGE, $pagecode)) {
				$groups_text = implode('</div><div class="l">', $this->model->buffer);
				if (!$groups_text) {
					$groups_text = 'Не назначено ни одной группы!';
				} else {
					$groups_text = '<div class="l">' . $groups_text . '</div>';
				}
				$this->setTemplateError('<br/>У вас нет прав доступа к запрашиваемому веб-документу!<br/><br/>Документ доступен только следующим группам пользователей:<br/><br/>' . $groups_text);
				*/
				
				$model = $this->model;
				
				$data = $model->jsData;
				$data->entity = new TObject();
				$data->detail = new TObject();
				
				if (! getCache('page_detail', $url_name, $model->detail)) {
					$this->model->detail = $this->getDetail($url_name);
					setCache('page_detail', $url_name, $model->detail, self::TTL);
				}
				
				$detail = $model->detail;
				
				// Comments
				if (! getCache('comments_page', $url_name, $model->comments)) {
					$commentsController = Page::addGComponent('comments');
					$commentsController->run( Array('entity_type' => 'page', 'entity_id' => $detail->id) );
					$model->comments = $commentsController->model;
					setCache('comments_page', $url_name, $model->comments, self::TTL);
				}
				
				$model->title = Utils::getAElement($model->config['main'], 'orgcode') . Utils::getAElement($model->config['main'], 'title_sep') . $detail->caption;
				
				// Meta tags
				$model->meta[] = Array('property' => 'og:title', 'content' => str_replace('"', '&quot;', $detail->caption));
				$model->meta[] = Array('property' => 'og:description', 'content' => str_replace('"', '&quot;', $detail->metadesc));
				//TODO: $this->model->meta[] = Array('property' => 'og:image', 'content' => $this->model->detail->image);
				
				$entity = $data->entity;
				$entity->type = $this->model->entity->type; //$model->entity_type;
				$entity->id = $detail->id;
				
				$datadetail = $data->detail;
				$datadetail->hits = $detail->hits;
				$datadetail->votes = $detail->votes;
				$datadetail->votesCount = $detail->votes_count;
				
				// hits
				$pagesHits = Utils::_SESSION('pages_hits');
				if (! $pagesHits) $pagesHits = Array();
				$datadetail->viewed = (in_array($detail->id, $pagesHits) ? 1 : 0);
				
				return TRUE;
				
			} else {
				
				return FALSE;
			}
		}
	}
	
	public function getDetail( $pagecode ) {
		
		$qr = DB::execute('
			SELECT
				d.id, d.docdate, d.upddate, d.body, d.caption, d.url_name, d.author_id, d.hits, d.tags, d.flag, d.metadesc,
				d.srcinfo, d.isallowvote, d.isallowcomments, d.votes, d.votes_count, u.login author_login, u.fullname author_name
			FROM web_pages d
				LEFT JOIN sec_user u ON (u.id = d.author_id)
			WHERE (d.url_name = ?)
			ORDER BY d.docdate desc',
			$pagecode
		);
		
		if (! ($detail = DB::fetch_object($qr))) return Request::stop('Запрашиваемая страница не найдена!', Request::HH_NOTFOUNDED, 'Запрашиваемая страница не найдена!');
		
		// hits
		$detail->hitsPrint = ($detail->hits < 10 ? 'менее 10' : $detail->hits);
		
		// votes
		CommonUtils::prepareVotesData($detail);
		
		$detail->author = ($detail->author_login ? Array( 'author_name' => $detail->author_name, 'author_login' => $detail->author_login ) : null);
		
		WebUtils::prepareTags($detail);
		WebUtils::prepareSrcInfo($detail);
		
		if ($detail->flag == 1) {
			$detail->only_body = true;
		}
			
		return $detail;
	}
	
	/**
	 * Учесть голос (оценку)
	 */
	public function voting($pagecode) {
		$resultController = Page::addComponentAndRun('voting');
		if ($resultController->model->success) {
		  clearCache('page', $pagecode);
		}
		return json_encode($resultController->model);
	}
	
	/**
	 * Добавить коммент
	 */
	public function commentsRun() {
		$resultController = Page::addGComponent('comments');
		$resultController->run( Array('entity_type' => 'page', 'entity_id' => Utils::_REQUEST('entity_id')) );
		if ($resultController->model->success) {
		  clearCache('page', $pagecode);
		}
		return json_encode($resultController->model);
	}
}
?>
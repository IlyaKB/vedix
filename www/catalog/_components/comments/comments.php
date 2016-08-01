<?php
namespace VediX;

class CommentsController extends Controller {
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
	}
	
	public function run( $params = Array() ) {
		
		$resultEntityTables = $this->getEntityTables($params);
		
		if (Request::AJAX()) {
			if (! $resultEntityTables) {
				EXIT(json_encode($this->model));
			}
			if (Request::JX() != 'comments') {
				Request::stop('Получена не поддерживаемая команда!', Request::HH_ACCESSERROR);
			}
			switch (Utils::_REQUEST('action')) {
				case 'add': {
					$this->add($params);
					EXIT(json_encode($this->model));
				}
				case 'add_reply': EXIT($this->addReply($params));
				case 'vote': EXIT($this->vote($params));
			}
		} else {
			if ($resultEntityTables) {
				$this->getList($params);
			}
		}
	}
	
	private function getList($params) {
		
		$this->model->items = Array();
		
		$qr = DB::execute('SELECT c.id, c.author_id, c.author_name, su.photo, c.creation_date, c.text, c.counter_like, c.counter_dislike
FROM '.$this->entityTables->comments.' c
LEFT JOIN sec_user su ON (su.id = c.author_id)
WHERE (c.entity_type = ?) AND (c.entity_id = ?) AND (c.parent_id IS NULL) AND (c.status = 1)
ORDER BY c.creation_date DESC', $params['entity_type'], $params['entity_id']);
		$index = 0;
		while ($comment = DB::fetch_object($qr)) {
			$index++;
			$comment->index = $index;
			if (! $comment->photo) $comment->photo = '/data/site/user/avatar_default_x.png';
			$comment->replies = Array();

			$qr2 = DB::execute('SELECT c.id, c.author_id, c.author_name, su.photo, c.creation_date, c.text, c.counter_like, c.counter_dislike
FROM '.$this->entityTables->comments.' c
LEFT JOIN sec_user su ON (su.id = c.author_id)
WHERE (c.entity_type = ?) AND (c.entity_id = ?) AND (c.parent_id = ?) AND (c.status = 1)
ORDER BY c.creation_date DESC', $params['entity_type'], $params['entity_id'], $comment->id);
			$index2 = 0;
			while ($reply = DB::fetch_object($qr2)) {
				$index2++;
				$reply->index = $index2;
				if (! $reply->photo) $reply->photo = '/data/site/user/avatar_default_x.png';
				$comment->replies[] = $reply;
			}

			$this->model->items[] = $comment;
		}
	}
	
	private function add($params) {
		
		$this->model->success = '';
		$this->model->warning = '';
		$this->model->error = '';
		
		$entity_type = $params['entity_type'];
		/*switch ($entity_type) {
			case 'page': case 'state': case 'news': case 'blog': case 'magposition': break;
			default: {
				$this->model->error = 'Error! Entity type "'.$entity_type.'" is not valid!';
				return false;
			}
		}
			
		//$entityTables = Array('page' => 'web_pages', 'state' => 'web_states', 'news' => 'web_news', 'blog' => 'web_blog_posts', 'magposition' => 'mag_positions');
		//$table = $entityTables[$entity_type];*/

		$entity_id = (int)$params['entity_id'];
		$authorName = (User::id() ? User::$fullname : Utils::_REQUEST('author_name'));
		$email = Utils::_REQUEST('email');
		$text = Utils::_REQUEST('text');


		$text = preg_replace('/<[\s]*[i]?frame.*?>|<\/[\s]*[i]?frame[\s]*>/i', '', $text);
		$text = preg_replace('/<[\s]*script.*?>|<\/[\s]*script[\s]*>/i', '', $text);
		$text = preg_replace('/<[\s]*[\w]*.*?>|<\/[\s]*[\w]*[\s]*>/i', '', $text);
		$text = preg_replace('/\[url=(.*?)](.*?)\[\/url\]/i', '<a rel="nofollow" href="/redirect/\\1">\\2</a>', $text);
		$text = preg_replace('/\[a\](.*?)\[\/a\]/i', '<a rel="nofollow" href="/redirect/\\1">\\1</a>', $text);
		$text = preg_replace('/\s(http[s]?:\/\/[^\s]+)/i', '<a rel="nofollow" href="/redirect/\\1">\\1</a>', $text);
		$text = preg_replace('/\[b\](.*?)\[\/b\]/i', '<b>\\1</b>', $text);
		$text = preg_replace('/\[i\](.*?)\[\/i\]/i', '<i>\\1</i>', $text);
		$text = preg_replace('/\[u\](.*?)\[\/u\]/i', '<u>\\1</u>', $text);
		$text = preg_replace('/\[s\](.*?)\[\/s\]/i', '<s>\\1</s>', $text);
		$text = preg_replace('/\[img\](.*?)\[\/img\]/i', '<img src="\\1"/>', $text);
		$text = preg_replace('/\[quote\](.*?)\[\/quote\]/i', '<blockquote>\\1</blockquote>', $text);

		DB::execute('INSERT INTO '.$this->entityTables->comments.' (entity_type, entity_id, parent_id, author_id, author_name, creation_date, text, status) VALUES (?,?,NULL,?,?,NOW(),?,1)',
			$entity_type, $entity_id, User::id(), $authorName, $text);

		$comment_id = DB::lastID();

		$qr = DB::execute('SELECT c.id, c.author_id, c.author_name, su.photo, c.creation_date, c.text, c.counter_like, c.counter_dislike
FROM '.$this->entityTables->comments.' c
LEFT JOIN sec_user su ON (su.id = c.author_id)
WHERE (c.id = ?)', $comment_id);
		$this->model->comment = DB::fetch_object($qr);
		$this->model->comment->author_id = (int)$this->model->comment->author_id;
		if (! $this->model->comment->photo) $this->model->comment->photo = '/data/site/user/avatar_default_x.png';

		$this->model->success = 'Комментарий успешно добавлен';

		$key = DB::getValues('url_name', $this->entityTables->entity, 'id = ?', $entity_id);
		if ($key) {
			clearCache('comments_' . $entity_type, $key);
		}
		
		return true;
	}
	
	private function vote() {
		
		$comment_id = (int)Utils::_REQUEST('id');
		$typeStr = Utils::_REQUEST('type') == 'like' ? 'like' : 'dislike';
		$typeSign = ($typeStr == 'like' ? '+' : '-');
		$type2Str = ($typeStr == 'like' ? 'dislike' : 'like');
		$type2Sign = ($typeSign == '+' ? '-' : '+');
		
		$success = '';
		$warning = '';
		
		$sessionVotes = Utils::_SESSION($this->entityTables->comments);
		if (! $sessionVotes) $sessionVotes = Array();
		if ( ( (in_array($comment_id . '+', $sessionVotes)) && ($typeSign == '+') ) || ( (in_array($comment_id . '-', $sessionVotes)) && ($typeSign == '-') ) ) {
			DB::execute('UPDATE '.$this->entityTables->comments.' SET counter_' . $typeStr . ' = counter_' . $typeStr . ' - 1 WHERE (id = ?)', $comment_id);
			$sessionVotes = Utils::deleteArrayElementByValue($sessionVotes, $comment_id . $typeSign);
			Utils::_SESSION($this->entityTables->comments, $sessionVotes);
			$warning = 'Вы забрали свой голос за вопрос!';
		} else if ( ( (in_array($comment_id . '+', $sessionVotes)) && ($typeSign == '-') ) || ( (in_array($comment_id . '-', $sessionVotes)) && ($typeSign == '+') ) ) {
			DB::execute('UPDATE '.$this->entityTables->comments.' SET counter_' . $typeStr . ' = counter_' . $typeStr . ' + 1, counter_' . $type2Str . ' = counter_' . $type2Str . ' - 1 WHERE (id = ?)', $comment_id);
			$sessionVotes = Utils::deleteArrayElementByValue($sessionVotes, $comment_id . $type2Sign);
			$sessionVotes[] = $comment_id . $typeSign;
			Utils::_SESSION($this->entityTables->comments, $sessionVotes);
			$success = 'Вы переголосовали за вопрос!';
		} else {
			DB::execute('UPDATE '.$this->entityTables->comments.' SET counter_' . $typeStr . ' = counter_' . $typeStr . ' + 1 WHERE (id = ?)', $comment_id);
			$sessionVotes[] = $comment_id . $typeSign;
			Utils::_SESSION($this->entityTables->comments, $sessionVotes);
			$success = 'Ваш голос учтён!';
		}
		
		list($counter_like, $counter_dislike, $entity_type, $entity_id) = DB::getValues('counter_like, counter_dislike, entity_type, entity_id', $this->entityTables->comments, 'id = ?', $comment_id);
		
		//$entityTables = Array('page' => 'web_pages', 'state' => 'web_states', 'news' => 'web_news', 'blog' => 'web_blog_posts', 'magposition' => 'mag_positions');
		//$table = $entityTables[$entity_type];
		$key = DB::getValues('url_name', $this->entityTables->entity, 'id = ?', $entity_id);
		if ($key) {
			clearCache('comments_' . $entity_type, $key);
		}
		
		return '{"success": "'.$success.'", "warning": "'.$warning.'", "counter_like": '.(int)$counter_like.', "counter_dislike": '.(int)$counter_dislike.'}';
	}
	
	private function getEntityTables($params) {
		
		$entity_type = $params['entity_type'];
		switch ($entity_type) {
			case 'page': case 'state': case 'news': case 'blog': case 'magposition': break;
			default: {
				$this->model->error = 'Error! Entity type "'.$entity_type.'" is not valid!';
				return false;
			}
		}
		
		$this->entityTables = new TObject();
		
		$entityTables = Array('page' => 'web_pages', 'state' => 'web_states', 'news' => 'web_news', 'blog' => 'web_blog_posts', 'magposition' => 'mag_positions');
		$this->entityTables->entity = $entityTables[$entity_type];
		
		$commentTables = Array('page' => 'web_comments', 'state' => 'web_comments', 'news' => 'web_comments', 'blog' => 'web_comments', 'magposition' => 'mag_comments');
		$this->entityTables->comments = $commentTables[$entity_type];

		return true;
	}
}

class CommentsModel extends Model {
	
	// Если ресурсы находятся там же что и контроллер компонента, то можно просто указывать имена файлов
	/*public $resources = Array(
		'comments.css'
	);*/
}
?>
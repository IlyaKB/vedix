<?php
namespace VediX;

class PollController extends Controller {
	
	const TTL = 600;
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
	}
	
	public function run( $params = Array() ) {
		
		if (Request::AJAX()) {
			if (Request::JX() != 'poll') {
				Request::stop('Получена не поддерживаемая команда!', Request::HH_ACCESSERROR);
			}
			return $this->vote();
		} else {
			
			$poll_id = (int)Utils::getAElement($params, 'id', null);
		
			if (! $poll_id) $poll_id = $this->getNonVotedPoll();
			if (! $poll_id) $poll_id = $this->getRandomPoll();

			if ($poll_id) {
				if (! getCache('poll', $poll_id, $this->model->poll)) {
					$this->model->poll = $this->getPoll($poll_id);
					setCache('poll', $poll_id, $this->model->poll, self::TTL);
				}
			}
		}
	}
	
	public function getPoll( $id ) {
		
		$colors = Array('#f00', '#ff0', '#0f0', '#0ff', '#00f', '#f0f', '#e1e1e1', '#ed008c', '#00aeef', '#00a650', '#fbaf5a', '#acd372', '#16bcb4', '#7ca6d8', '#a286bd', '#c2c2c2');
		
		$qr = DB::execute('SELECT id, question, type, status FROM web_poll WHERE (id = ?)', $id);
		$poll = DB::fetch_object($qr);
		
		$items = Array();
		$max_votes = 1;
		$colors_use = Array();
		$qr = DB::execute('SELECT id, text, votes, color FROM web_poll_items WHERE (poll_id = ?) ORDER BY number', $id);
		while ($item = DB::fetch_object($qr)) {
			$items[] = $item;
			if ($max_votes < $item->votes) $max_votes = $item->votes;
			if ($item->color) $colors_use[] = $item->color;
		}
		$poll->max_votes = $max_votes;
		
		foreach ($items as &$item) {
			$item->votes_per = round(100 * $item->votes / $max_votes);
			if (! $item->color) {
				foreach ($colors as $color) {
					if (! in_array($color, $colors_use)) {
						$item->color = $color;
						$colors_use[] = $color;
						break;
					}
				}
			}
			if (! $item->color) $item->color = 'gray'; // default color
		}
		
		$poll->items = $items;
		
		$poll->voted = $this->check_vote( $id ) ? 1 : '';
		
		return $poll;
	}
	
	/**
	 * Учёт голосов
	 */
	public function vote() {
		
		$this->model->success = '';
		$this->model->warning = '';
		$this->model->error = '';
		
		$poll_id = (int)Utils::_REQUEST('poll_id');
		
		if (! $poll_id) {
			$this->model->error = 'Передан не верный ИД опроса (poll_id)!';
			return false;
		}
		
		// Проверка на повтор голосования
		if ($this->check_vote( $poll_id )) {
			$this->model->warning = 'Вы уже проголосовали в этом опросе!';
			return false;
		}
		
		$items_checked = Utils::_REQUEST('items_checked');
		
		$q_items = 0;
		$sql = 'UPDATE web_poll_items SET votes = IFNULL(votes, 0) + 1 WHERE (id = ?)';
		foreach ($items_checked as $val) {
			$q_items++;
			$qr = DB::execute($sql, (int)$val);
		}
		
		if (! $q_items) {
			$this->model->warning = 'Выберите хотя бы один вариант!';
			return false;
		}
		
		$ip = null;
		$sessionid = null;
		if (! Session::getSessionID()) {
			$ip = Session::getIP();
			$sessionid = Session::getSessionIDText();
		}
		
		$qr = DB::execute(
			'INSERT INTO web_votes (object_type, object_id, session_id, ip, sessionid, value, votetime) VALUES (?,?,?,?,?,?,NOW())',
			'poll', $poll_id, Session::getSessionID(), $ip, $sessionid, implode(',', $items_checked)
		);
		
		// Расчёт новых max_votes и votes
		$items = Array();
		$max_votes = 1;
		$qr = DB::execute('SELECT id, votes FROM web_poll_items WHERE (poll_id = ?)', $poll_id);
		while ($item = DB::fetch_object($qr)) {
			$items[] = $item;
			$item->id = (int)$item->id;
			if ($max_votes < $item->votes) $max_votes = $item->votes;
		}
		foreach ($items as &$item) {
			$item->votes_per = round(100 * $item->votes / $max_votes);
		}
		$this->model->items = $items;
		$this->model->max_votes = $max_votes;
		
		$this->model->success = 'Вы успешно проголосовали!';
		
		return true;
	}
	
	public function check_vote( $poll_id ) {
		
		if (User::isAuthorized()) {
			
			if (DB::getValues( // Проверка по user_id
				'count(*)',
				'web_votes v LEFT JOIN crs_session_history h ON (h.id = v.session_id)',
				'(v.object_type = "poll") AND (v.object_id = ?) AND (v.session_id = ?) AND (h.user_id = ?)',
				Array( $poll_id, Session::getSessionID(), User::id() )
			)) {
				return true;
			}
			
		} else {
			
			$repeatmode = (int)DB::getValues('repeatmode', 'web_poll', 'id = ?', $poll_id);
			
			switch ($repeatmode) {
				case 0: { // Самая "демократическая" - Можно повторно голосовать каждые 1 час
					if (DB::getValues(
						'count(*)',
						'web_votes v',
						'(v.object_type = "poll") AND (v.object_id = ?) AND (v.session_id = ?) AND (UNIX_TIMESTAMP(v.votetime) + 3600 > ?)',
						Array( $poll_id, Session::getSessionID(), time() )
					)) {
						return true;
					}
					break;
				}
				case 1: { // Обычная - Проверка по ИД сессии
					if (DB::getValues(
						'count(*)',
						'web_votes v',
						'(v.object_type = "poll") AND (v.object_id = ?) AND (v.session_id = ?)',
						Array( $poll_id, Session::getSessionID() )
					)) {
						return true;
					}
					break;
				}
				case 2: { // Самая жесткая - Проверка по IP-адресу+session_id и сроку давности сессий (24 часа)
					$select_sql = '';
					$params_sql = '';
					$params = Array();
					if ( (Session::getIPID()) && (Session::getSessionID()) ) {
						$select_sql = 'web_votes v LEFT JOIN crs_session_history h ON (h.id = v.session_id) AND (UNIX_TIMESTAMP(h.stime) + 86400 > ?)';
						$params_sql = '(v.object_type = "poll") AND (v.object_id = ?) AND ( (h.ip_id = ?) OR (v.session_id = ?) )';
						$params = Array( time(), $poll_id, Session::getIPID(), Session::getSessionID() );
					} else {
						$select_sql = 'web_votes v';
						$params_sql = '(v.object_type = "poll") AND (v.object_id = ?) AND ( (v.ip = ?) OR (v.sessionid = ?) ) AND (UNIX_TIMESTAMP(v.votetime) + 86400 > ?)';
						$params = Array( $poll_id, Session::getIP(), Session::getSessionIDText(), time() );
					}
					if (DB::getValues(
						'count(*)',
						$select_sql,
						$params_sql,
						$params
					)) {
						return true;
					}
					break;
				}
			}
		}
		return false;
	}
	
	private function getNonVotedPoll() {
		$qr = DB::execute('SELECT p.id
			FROM web_poll p
				LEFT JOIN web_votes v ON (v.object_type = "poll") AND (v.object_id = p.id) AND ( (v.ip = ?) OR (v.sessionid = ?) ) AND (UNIX_TIMESTAMP(v.votetime) + 86400 > ?)
				LEFT JOIN crs_session_history h ON (h.id = v.session_id) AND (UNIX_TIMESTAMP(h.stime) + 86400 > ?)
			WHERE (p.status = 1) AND (v.id IS NULL) AND (h.id IS NULL)
			LIMIT 0,1', Session::getIP(), Session::getSessionIDText(), time(), time());
		return DB::fetch_val($qr);
	}
	
	private function getRandomPoll() {
		$qr = DB::execute('SELECT p.id FROM web_poll p WHERE (p.status = 1) LIMIT 0,100');
		$ids = Array();
		$q = 0;
		while ($item = DB::fetch_object($qr)) {
			$q++;
			$ids[] = $item->id;
		}
		if ($q) {
			$index = rand(0, $q - 1);
			return $ids[$index];
		}
		return null;
	}
}

class PollModel extends Model {
	
	// Если ресурсы находятся там же что и контроллер компонента, то можно просто указывать имена файлов
	/*public $resources = Array(
		'comments.css'
	);*/
	
	public $id = null;
	public $poll = null;
}
?>
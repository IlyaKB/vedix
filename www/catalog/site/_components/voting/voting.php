<?php
namespace VediX;

class VotingController extends Controller {
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
	}
	
	public function run( $params = Array() ) {
		
		$success = '';
		$warning = '';
		$error = '';
		$votes = 0;
		$votes_count = 0;
		
		$entity_type = Utils::_REQUEST('entity_type');
		switch ($entity_type) {
			case 'page': case 'state': case 'blog': case 'news': break;
			default: {
				$error = 'Error! Entity type "'.$entity_type.'" is not valid!';
			}
		}
		
		if (! $error) {
			
			$entityTables = Array('page' => 'web_pages', 'state' => 'web_states', 'news' => 'web_news', 'blog' => 'web_blog_posts');
			$table = $entityTables[$entity_type];
			
			$entity_id = (int)Utils::_REQUEST('entity_id');
			$mark = (int)Utils::_REQUEST('mark');
			$sessionKey = $entity_type . '_voting_' . $entity_id;

			//Utils::_SESSION($sessionKey, null);
			$sessionMark = Utils::_SESSION($sessionKey);
			list($votes_count_previous, $votes_previous) = DB::getValues('votes_count, votes', $table, 'id = ?', $entity_id);
			$votes_count_next = ($sessionMark ? $votes_count_previous - 1 : $votes_count_previous + 1);
			
			if ($sessionMark) {
				if ($votes_count_next <= 0) {
					DB::execute('UPDATE '.$table.' SET votes_count = 0, votes = 0 WHERE (id = ?)', $entity_id);
				} else {
					// 3 голоса: 1, 3, 5 => 3
					// отозван голос "1 балл": x = (3 - 1 / 3) / 2 * 3 = 4 - ok
					$votes = ($votes_previous - $sessionMark / $votes_count_previous) * $votes_count_previous / $votes_count_next;
					DB::execute('UPDATE '.$table.' SET votes_count = IFNULL(votes_count,0) - 1, votes = ? WHERE (id = ?)', $votes, $entity_id);
				}
				Utils::_SESSION($sessionKey, null);
				$warning = 'Вы забрали свой голос!';
			} else {
				// 2 голоса: 3, 5 => 4
				// добавлен 3-й голос "1 балл": x = 4 * 2 / 3 + 1 / 3 = 3 - ok
				$votes = $votes_previous * $votes_count_previous / $votes_count_next + $mark / $votes_count_next;
				DB::execute('UPDATE '.$table.' SET votes_count = IFNULL(votes_count,0) + 1, votes = ? WHERE (id = ?)', $votes, $entity_id);
				Utils::_SESSION($sessionKey, $mark);
				$success = 'Ваш голос учтён!';
			}
		}
		
		$this->model->success = $success;
		$this->model->warning = $warning;
		$this->model->error = $error;
		$this->model->votes = $this->getVotesArray($votes);
		$this->model->votes_count = $votes_count_next;
	}
	
	private function getVotesArray($votes) {
	  $vote_round0 = round($votes);
		$result = Array(0,0,0,0,0);
		for ($i = 1; $i <= 5; $i++) {
			if ($vote_round0 >= $i) $result[$i-1] = 1;
		}
		return $result;
	}
}
?>
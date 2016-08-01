<?php
namespace VediX;

//define('HD_FAQ', HD_SITE . 'faq/');

class FaqController extends SiteController {
	
	const TTL = 600;
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
		
		$this->model = new FaqModel();
	}
	
	public function run() {
		
		parent::run();
		
		if (Request::AJAX()) {
			switch (Request::JX()) {
				case 'addquestion': EXIT($this->addQuestion());
				case 'vote': EXIT($this->vote());
				default: {
					Request::stop('Получена не поддерживаемая команда!', Request::HH_ACCESSERROR);
				}
			}
		} else {
			parent::run();
			
			if (! getCache('faq_categories', null, $this->model->categories)) {
				$this->model->categories = $this->getCategories();
				setCache('faq_categories', null, $this->model->categories, self::TTL);
			}
			
			return TRUE;
		}
	}
	
	private function getCategories() {
		
		$categories = Array();
		
		$qr = DB::execute('SELECT id, code, name FROM web_faq_category WHERE (status = 1) ORDER BY number;');
		while ($category = DB::fetch_object($qr)) {
			$category->questions = $this->getQuestions($category->id);
			$categories[] = $category;
		}
		
		return $categories;
	}
	
	private function getQuestions($category_id) {
		
		$questions = Array();
		
		$qr = DB::execute('SELECT id, question, reply, counter_like, counter_dislike FROM web_faq_question WHERE (category_id = ?) AND (status = 1) ORDER BY number;', $category_id);
		while ($question = DB::fetch($qr)) {
			$questions[] = $question;
		}
		
		return $questions;
	}
	
	private function addQuestion() {
		$category_id = Utils::_REQUEST('category_id');
		if (! $category_id) $category_id = FaqModel::DEFAULT_CATEGORY_ID;
		$text = Utils::_REQUEST('text');
		if (! $text) EXIT('{"error": "Заполните поле текста вопроса!"}');
		$qr = DB::execute('INSERT INTO web_faq_question (category_id, question, status) VALUES (?,?,null)', $category_id, $text);
		return '{"success": "Ваш вопрос отправлен на обработку администратором сайта"}';
	}
	
	private function vote() {
		
		$question_id = (int)Utils::_REQUEST('id');
		$typeStr = Utils::_REQUEST('type') == 'like' ? 'like' : 'dislike';
		$typeSign = ($typeStr == 'like' ? '+' : '-');
		$type2Str = ($typeStr == 'like' ? 'dislike' : 'like');
		$type2Sign = ($typeSign == '+' ? '-' : '+');
		
		$success = '';
		$warning = '';
		
		$sessionVotes = Utils::_SESSION('faq_votes');
		if (! $sessionVotes) $sessionVotes = Array();
		if ( ( (in_array($question_id . '+', $sessionVotes)) && ($typeSign == '+') ) || ( (in_array($question_id . '-', $sessionVotes)) && ($typeSign == '-') ) ) {
			DB::execute('UPDATE web_faq_question SET counter_' . $typeStr . ' = counter_' . $typeStr . ' - 1 WHERE (id = ?)', $question_id);
			$sessionVotes = Utils::deleteArrayElementByValue($sessionVotes, $question_id . $typeSign);
			Utils::_SESSION('faq_votes', $sessionVotes);
			$warning = 'Вы забрали свой голос за вопрос!';
		} else if ( ( (in_array($question_id . '+', $sessionVotes)) && ($typeSign == '-') ) || ( (in_array($question_id . '-', $sessionVotes)) && ($typeSign == '+') ) ) {
			DB::execute('UPDATE web_faq_question SET counter_' . $typeStr . ' = counter_' . $typeStr . ' + 1, counter_' . $type2Str . ' = counter_' . $type2Str . ' - 1 WHERE (id = ?)', $question_id);
			$sessionVotes = Utils::deleteArrayElementByValue($sessionVotes, $question_id . $type2Sign);
			$sessionVotes[] = $question_id . $typeSign;
			Utils::_SESSION('faq_votes', $sessionVotes);
			$success = 'Вы переголосовали за вопрос!';
		} else {
			DB::execute('UPDATE web_faq_question SET counter_' . $typeStr . ' = counter_' . $typeStr . ' + 1 WHERE (id = ?)', $question_id);
			$sessionVotes[] = $question_id . $typeSign;
			Utils::_SESSION('faq_votes', $sessionVotes);
			$success = 'Ваш голос учтён!';
		}
		
		clearCache('faq_categories');
		
		$votes = DB::getValues('counter_like, counter_dislike', 'web_faq_question', 'id = ?', $question_id);
		return '{"success": "'.$success.'", "warning": "'.$warning.'", "counter_like": '.(int)$votes[0].', "counter_dislike": '.(int)$votes[1].'}';
	}
}

class FaqModel extends SiteModel {
	
	const DEFAULT_CATEGORY_ID = 1; // Прочие
	
	public $resources_module = Array(
		Array('css' => 1, 'head' => 1, 'href' => '/catalog/site/faq/faq.css')
	);
	
	public $categories = Array();
	
	public function __construct() {
		parent::__construct();
	}
}
?>
<?php

class CommonUtils {
	
	static public function paginationBuild($entity_url, $page, $total, $page_rec_count = 10) {
		
		$paginationCount = floor(($total - 1) / $page_rec_count) + 1;
		
		if ($paginationCount == 1) return NULL;
		
		$p_begin = max(1, $page - 3);
		$p_end = min($paginationCount, $page + 3);

		$pagination = new TObject();
		$pagination->items = Array();
		if ($page > 1) {
			$pagination->previous_page = ( $page - 1 == 1 ? '/' . $entity_url : '?p='.($page - 1) );
		}
		$pagination->next_page = ($page < $paginationCount ? $page + 1 : null);
		
		function createItem($entity_type, $num, $text = '', $selected = false, $flag = '') {
			$item = new TObject();
			$item->num = $num;
			$item->text = ($text ? $text : $num);
			$item->href = ($num == 1 ? '/' . $entity_type : '?p=' . $num);
			$item->selected = $selected;
			return $item;
		}

		if ($paginationCount <= 12) { // Выводим всё
			for ($p = 1; $p <= $paginationCount; $p++) {
				$pagination->items[] = createItem($entity_url, $p, $p, ($page == $p ? true : false) );
			}
		} else { // Возможны пропуски "..."

			function fillBegin() {
				$pagination->items[] = createItem($entity_url, 1);
				$pagination->items[] = createItem($entity_url, 2);
				$pagination->items[] = createItem($entity_url, '...', $page - 7, false, '<' );
			}
			function fillEnd() {
				$pagination->items[] = createItem($entity_url, '...', $page + 7, false, '>');
				$pagination->items[] = createItem($entity_url, $paginationCount - 1);
				$pagination->items[] = createItem($entity_url, $paginationCount);
			}

			if ($page <= 7) { // Находясь в начале списка всегда выводим первые 9 ссылок
				for ($p = 1; $p <= max(9, $page + 3); $p++) {
					$pagination->items[] = createItem($entity_url, $p, $p, ($page == $p ? true : false) );
				}
				fillEnd();
			} else if ($page >= $paginationCount - 6) { // Находясь в конце списка всегда выводим последние 9 ссылок
				fillBegin();
				for ($p = min($paginationCount - 8, $page - 3); $p <= $paginationCount; $p++) {
					$pagination->items[] = createItem($entity_url, $p, $p, ($page == $p ? true : false ));
				}
			} else { // Находясь в где-то середине выводим по 2 ссылки с краю и 7 в середине
				fillBegin();
				for ($p = $page - 3; $p <= $page + 3; $p++) {
					$pagination->items[] = createItem($entity_url, $p, $p, ($page == $p ? true : false) );
				}
				fillEnd();
			}
		}

		return $pagination;
	}
	
	static public function prepareVotesData(&$detail) {
		$detail->votes_print = (! $detail->votes ? 'нет' : $detail->votes);
		$vote_round0 = round($detail->votes);
		$detail->votes = round($detail->votes, 1);
		$votes = Array(0,0,0,0,0);
		for ($i = 1; $i <= 5; $i++) {
			if ($vote_round0 >= $i) $votes[$i-1] = 1;
		}
		$detail->votes = $votes;
		$detail->votes_count = (int)$detail->votes_count;
	}
}
?>
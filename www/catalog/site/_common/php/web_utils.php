<?php

/**
 * Utils for web documents (pages, news, blog, states)
 */
class WebUtils {
	
	static public function prepareSrcInfo(&$record) {
		if ($record->srcinfo) {
			$is_link = preg_match('/:\/\/.+\..+/', $record->srcinfo) ? true : false;
			$is_with_tag = preg_match('/<.+>/', $record->srcinfo) ? true : false;
			if ( ($is_link) && (! $is_with_tag) ) {
				$record->srcinfo = '<a href="' . $record->srcinfo . '">ссылка</a>';
			}
		}
	}
	
	static public function prepareTags(&$detail) {
		$tags = preg_replace('/[\s]*[,;\|]+[\s]*/i', ',', $detail->tags);
		$detail->tags = ($detail->tags ? Array('items' => explode(',', $tags)) : null);
	}
	
	static public function visit($entity_type, $entity_table, $url_name) {
		
		$result = new TObject();
		
		$docHits = Utils::_SESSION($entity_type . '_hits');
		if (! $docHits) $docHits = Array();
		
		list($id, $hits) = DB::getValues('id, hits', $entity_table, 'url_name = ?', $url_name);
		if (! $id) {
			$result->error = 'Error! Document with url_name = "' . $url_name . '" not founded!';
		} else {
			$result->hits = (int)$hits;
			if (! in_array($id, $docHits)) {
				$docHits[] = $id;
				$result->hits++;
				DB::execute('UPDATE '.$entity_table.' SET hits = IFNULL(hits, 0) + 1 WHERE (id = ?);', $id);
				clearCache($entity_type . '_detail', $url_name);
				Utils::_SESSION($entity_type . '_hits', $docHits);
			}
		}
		return json_encode($result);
	}
}
?>

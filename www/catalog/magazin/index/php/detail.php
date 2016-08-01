<?php
namespace VediX;

class MagazinDetail {

	static public function get( $key ) {
		
		$qr = DB::execute('SELECT
				r.id, r.name, r.url_name, r.photo_url, c.url_name AS category_url_name,
				r.is_alloworder, r.is_available, r.quantity, m.name_short measure_name, r.price,
				r.hits, r.isallowcomments, r.ispremoderation, r.isallowvote, r.votes_count, r.votes,
				r.dateplacement, r.description, r.manufacturer_id, mf.name AS manufacturer_name,
				(SELECT COUNT(*) FROM web_comments c WHERE (c.entity_type = "magposition") AND (c.entity_id = r.id)) AS comments_count
			FROM mag_positions r
				LEFT JOIN mag_manufacturers mf ON (mf.id = r.manufacturer_id)
				LEFT JOIN mag_categories c ON (c.id = r.category_id)
				LEFT JOIN mag_measures m ON (m.id = r.measure_id)
			WHERE (r.url_name = ?)',$key);
		
		if (! ($detail = DB::fetch_object($qr))) return Request::stop('Запрашиваемый товар (услуга) не найдены!', Request::HH_NOTFOUNDED, 'Запрашиваемая позиция не найдена!');
		
		if (Request::DEVELOPER_MODE()) {
			$detail->description = preg_replace('/http[s]?:\/\/.*[\/]?/i', ConfigSite::siteprotocol . '://' . ConfigSite::$sitedname . '/#DEVELOPER_MODE', $detail->description);
		}
		
		$detail->name = htmlentities($detail->name);
		$detail->dateplacement = Utils::getDateStr($detail->dateplacement);
		if (! is_file(HD_ROOT . $detail->photo_url)) {
			$detail->photo_url = '/data/magazin/photo_no.png';
		}
				
		// hits
		$detail->hitsPrint = ($detail->hits < 10 ? 'менее 10' : $detail->hits);
		
		// votes
		CommonUtils::prepareVotesData($detail);
		
		$detail->manufacturer = null;
		if ($detail->manufacturer_name) {
			$detail->manufacturer = Array(
				'name' => $detail->manufacturer_name,
				'id' => $detail->manufacturer_id
			);
		}
		
		// photos feature
		$detail->featurephotos = null;
		$qr = DB::execute('SELECT id, photo_url, name_alt FROM mag_galleries g WHERE (g.position_id = ?)', $detail->id);
		if (DB::rowsCount($qr)) {
			$detail->featurephotos = new TObject();
			$detail->featurephotos->items = Array();
			while ($photo = DB::fetch_object($qr)) {
				$detail->featurephotos->items[] = $photo;
			}
		}
		
		// composition
		$detail->composition = null;
		$qr = DB::execute('SELECT c.id, c.name, c.quantity, m.name_short measure_name_short
			FROM mag_composition c
				LEFT JOIN mag_measures m ON (m.id = c.measure_id)
			WHERE (c.position_id = ?)', $detail->id);
		if (DB::rowsCount($qr)) {
			$detail->composition = new TObject();
			$detail->composition->items = Array();
			$n = 0;
			while ($item = DB::fetch_object($qr)) {
				$item->number = (++$n);
				$detail->composition->items[] = $item;
			}
		}
		
		return $detail;
	}
}
?>
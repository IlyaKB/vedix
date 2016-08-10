<?php
namespace VediX;

class PagesAdmin extends SectionDefaultController {
	
	public function formOpen() {
		$form = parent::formOpen();
		$form->jsFiles = Array(
			/*'/core/libs/elrte-1.3/js/jquery-migrate-1.2.1.min.js',
			'/core/libs/elrte-1.3/js/elrte.full.js',
			'/core/libs/elrte-1.3/js/i18n/elrte.ru.js',
			'/core/libs/elfinder-2.0/js/elfinder.min.js'*/
		);
		$form->cssFiles = Array(
			/*'/core/libs/elrte-1.3/css/elrte.full.css',
			'/core/libs/elfinder-2.0/css/elfinder.min.css',
			'/core/libs/elfinder-2.0/css/theme.css'*/
		);
		return $form;
	}
	
	public function jxSwitch() {
		$jx = Request::JX();
		$response = null;
		switch ($jx) {
			case 'loadData': $response = $this->getListData(); break;
			case 'entityLoadData': $response = $this->getEntityData(); break;
			case 'entityAdd': $response = $this->saveData(); break;
			case 'entitySaveData': $response = $this->saveData(); break;
			case 'entityDelete': $response = $this->deleteEntity(); break;
			default: {
				RETURN Request::stop(
					'Получена неизвестная команда!'.Utils::rn.Utils::rn.
					'Веб-приложение: '.$this->section['application'].Utils::rn.
					'Раздел: '.$this->section['code'].Utils::rn.
					'jx: '.$jx.Utils::rn,
					Request::HH_INTERNALERROR,
					'Получена неизвестная команда!'
				);
			}
		}
		return $response;
	}
	
	function getListData() {
	
		//$data = new TObject();

		$pagenum = $_GET['pagenum'];
		$pagesize = $_GET['pagesize'];
		$start = $pagenum * $pagesize;
		$query = "SELECT SQL_CALC_FOUND_ROWS id, caption, url_name, docdate, upddate, hits FROM web_pages LIMIT $start, $pagesize";
		if (isset($_GET['sortdatafield'])) {
			$sortfield = $_GET['sortdatafield'];
			$sortorder = $_GET['sortorder'];
			//$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
			$result = DB::execute($query);
			$sql = "SELECT FOUND_ROWS() AS `found_rows`;";
			//$rows = mysql_query($sql);
			$rows = DB::execute($query);
			//$rows = mysql_fetch_assoc($rows);
			$rows = DB::fetch($rows);
			$total_rows = $rows['found_rows'];

			if ($sortfield != NULL) {

				if ($sortorder == "desc") {
					$query = "SELECT id, caption, url_name, docdate, upddate, hits FROM web_pages ORDER BY" . " " . $sortfield . " DESC LIMIT $start, $pagesize";
				} else if ($sortorder == "asc") {
					$query = "SELECT id, caption, url_name, docdate, upddate, hits FROM web_pages ORDER BY" . " " . $sortfield . " ASC LIMIT $start, $pagesize";
				}
				//$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
				$result = DB::execute($query);
			}
		} else {
			//$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
			$result = DB::execute($query);
			$sql = "SELECT FOUND_ROWS() AS `found_rows`;";
			//$rows = mysql_query($sql);
			$rows = DB::execute($sql);
			//$rows = mysql_fetch_assoc($rows);
			$rows = DB::fetch($rows);
			$total_rows = $rows['found_rows'];
		}

		//while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		while ($row = DB::fetch($result)) {
			$customers[] = array(
				'id' => $row['id'],
				'caption' => $row['caption'],
				'url_name' => $row['url_name'],
				'docdate' => $row['docdate'],
				'upddate' => $row['upddate'],
				'hits' => $row['hits']
			);
		}
		$data[] = array(
			'TotalRows' => $total_rows,
			'Rows' => $customers
		);

		return $data;
	}
	
	private function getEntityData() {
	
		$id = Utils::_REQUEST('id');

		$qr = DB::execute('SELECT id, caption, url_name, body, status FROM web_pages WHERE (id = ?)', $id);
		$data = DB::fetch_object($qr);

		return $data;
	}
	
	function saveData() {
	
		$data = new TObject();

		$id = Utils::_REQUEST('id');
		$caption = stripslashes( Utils::_REQUEST('caption') );
		$url_name = Utils::_REQUEST('url_name');
		$body = stripslashes( Utils::_REQUEST('body') );
		$status = (int)Utils::_REQUEST('status');

		$msg = Array();
		if (! $caption) $msg[] = 'Не задан заголовок страницы!';
		if (! $url_name) $msg[] = 'Не задан код страницы!';
		if (isset($msg[0])) {
			RETURN Request::stop(
				'Не верно заполнены данные формы!'.Utils::rn.Utils::rn.
				implode(Utils::rn, $msg).Utils::rn,
				Request::HH_LOGICERROR,
				'Не верно заполнены данные формы!'
			);
		}

		if ($id) {
			$qr = DB::execute('SELECT url_name FROM web_pages WHERE (id = ?)', $id);
			$pagecode = DB::fetch_val($qr);
			clearCache('page_detail', $pagecode);

			$qr = DB::execute('UPDATE web_pages SET caption = ?, url_name = ?, body = ?, status = ?, upddate = Now() WHERE (id = ?)', $caption, $url_name, $body, $status, $id);
		} else {
			$qr = DB::execute('INSERT INTO web_pages (docdate, caption, url_name, body, status, upddate) VALUES (Now(), ?, ?, ?, ?, Now())', $caption, $url_name, $body, $status);
			$id = DB::lastID();
		}

		$qr = DB::execute('SELECT id, caption, url_name, docdate, upddate, hits FROM web_pages WHERE (id = ?)', $id);
		$data = DB::fetch_object($qr);

		// Редактирование текста главной страницы
		if ($id == 1) {
			$template = ConfigCatalog::get('skin', 'site', 'index');
			if ($template) {
				$filename = HD_ROOT . '/' . $template . '/site/index/content.html';
				if (is_file($filename)) {
					file_put_contents($filename, $body);
				}
			}
		}

		return $data;
	}

	private function deleteEntity() {

		$data = new TObject();

		$id = Utils::_REQUEST('id');

		$qr = DB::execute('SELECT url_name FROM web_pages WHERE (id = ?)', $id);
		$pagecode = DB::fetch_val($qr);

		clearCache('page_detail', $pagecode);

		$qr = DB::execute('DELETE FROM web_pages WHERE (id = ?)', $id);

		return $data;
	}
}
?>
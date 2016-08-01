<?php
namespace VediX;

class MainmenuAdmin extends SectionDefaultController {
	
	public function jxSwitch() {
		$jx = Request::JX();
		$response = null;
		switch ($jx) {
			case 'mainmenuLoad': $response = $this->mainmenuLoad(); break;
			case 'mainmenuAdd': $response = $this->mainmenuAdd(); break;
			case 'mainmenuSave': $response = $this->mainmenuSave(); break;
			case 'mainmenuDelete': $response = $this->mainmenuDelete(); break;
			case 'mainmenuUp': $response = $this->mainmenuUp(); break;
			case 'mainmenuDown': $response = $this->mainmenuDown(); break;
			case 'mainmenuGetPages': $response = $this->mainmenuGetPages(); break;
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
	
	private function mainmenuLoad() {

		$data = new TObject();

		$parent_id = (int)Utils::_REQUEST('parent_id');
		if (! $parent_id) $parent_id = 1;

		$data->rootitem = new TObject();

		$data->rootitem->childs = $this->getChilds($parent_id);

		return $data;
	}

	private function getChilds($parent_id) {
		$childs = Array();
		$qr = DB::execute('SELECT id, number, name, src, isnotinuse, isdemo FROM web_menu WHERE (parent_id = ?) ORDER BY number', $parent_id);
		while ($row = DB::fetch_object($qr)) {
			$item = new TObject();
			$item->id = $row->id;
			$item->number = $row->number;
			$item->name = $row->name;
			$item->src = $row->src;
			$item->status = ! $row->isnotinuse;
			$item->isdemo = !!$row->isdemo;
			array_push($childs, $item);
			$item->childs = $this->getChilds($row->id);
		}
		return $childs;
	}
	
	private function mainmenuSave() {

		$data = new TObject();

		$data->id = (int)Utils::_REQUEST('id');

		$data->number = DB::getValues('number', 'web_menu', 'id = ?', $data->id);
		$data->code = 'code' . $data->number;

		$data->name = Utils::_REQUEST('name');
		$data->src = Utils::_REQUEST('src');
		$status = (Utils::_REQUEST('status') == 'true' ? true : false);
		$data->isnotinuse = ($status ? 0: 1);
		$isdemo = (Utils::_REQUEST('isdemo') == 'true' ? true : false);
		$data->isdemo = $isdemo;

		$msg = Array();
		if (! $data->name) $msg[] = 'Не задано название пункта меню!';
		//if (! $data->src) $msg[] = 'Не задана ссылка пункта меню!';
		if (isset($msg[0])) {
			RETURN Request::stop(
				'Не верно заполнены данные формы!'.Utils::rn.Utils::rn.
				implode(Utils::rn, $msg).Utils::rn,
				Request::HH_LOGICERROR,
				'Не верно заполнены данные формы!'
			);
		}

		$qr = DB::execute(
			'UPDATE web_menu SET code = ?, name = ?, src = ?, isnotinuse = ?, isdemo = ? WHERE (id = ?)',
			$data->code, $data->name, $data->src, $data->isnotinuse, $data->isdemo, $data->id
		);

		clearCache('mainmenu');

		return $data;
	}
	
	private function mainmenuAdd() {

		$data = new TObject();

		$data->parent_id = (int)Utils::_REQUEST('parent_id');
		if (! $data->parent_id) $data->parent_id = 1;

		$qr = DB::execute('SELECT MAX(number) + 1 FROM web_menu WHERE (parent_id = ?)', $data->parent_id);
		$data->number = DB::fetch_val($qr);
		if (! $data->number) $data->number = 1;
		$data->code = '';
		$data->name = '';
		$data->src = '/';
		$data->isnotinuse = 0;
		$data->isdemo = null;

		$qr = DB::execute(
			'INSERT INTO web_menu (parent_id, number, code, name, src, isnotinuse, isdemo) VALUES (?,?,?,?,?,?,?)',
			$data->parent_id, $data->number, $data->code, $data->name, $data->src, $data->isnotinuse, $data->isdemo
		);

		$data->id = DB::lastID();

		return $data;
	}
	
	private function mainmenuDelete() {
	
		$data = new TObject();

		$id = Utils::_REQUEST('id');

		$qr = DB::execute('SELECT count(*) FROM web_menu WHERE (parent_id = ?)', $id);
		if (DB::fetch_val($qr)) {
			RETURN Request::stop(
				'Удалите сначала все вложенные записи!'.Utils::rn.Utils::rn.
				implode(Utils::rn, $msg).Utils::rn,
				Request::HH_LOGICERROR,
				'Удалите сначала все вложенные записи!'
			);
		}

		clearCache('mainmenu');

		$qr = DB::execute('DELETE FROM web_menu WHERE (id = ?)', $id);

		return $data;
	}
	
	private function mainmenuDown() {

		$data = new TObject();

		$id = (int)Utils::_REQUEST('id');
		if (! $id) return false;

		list($parent_id, $number) = DB::getValues('parent_id, number', 'web_menu', 'id = ?', $id);

		$qr = DB::execute('SELECT MIN(number) FROM web_menu WHERE (parent_id = ?) AND (number > ?)', $parent_id, $number);
		$_number = DB::fetch_val($qr);
		if (! $_number) return new TObject();

		DB::execute('UPDATE web_menu SET number = ? WHERE (parent_id = ?) AND (number = ?)', $number, $parent_id, $_number);
		DB::execute('UPDATE web_menu SET number = ? WHERE (id = ?)', $_number, $id);

		clearCache('mainmenu');

		return $data;
	}
	
	private function mainmenuUp() {

		$data = new TObject();

		$id = (int)Utils::_REQUEST('id');
		if (! $id) return false;

		list($parent_id, $number) = DB::getValues('parent_id, number', 'web_menu', 'id = ?', $id);

		$qr = DB::execute('SELECT MAX(number) FROM web_menu WHERE (parent_id = ?) AND (number < ?)', $parent_id, $number);
		$_number = DB::fetch_val($qr);
		if (! $_number) return new TObject();

		DB::execute('UPDATE web_menu SET number = ? WHERE (parent_id = ?) AND (number = ?)', $number, $parent_id, $_number);
		DB::execute('UPDATE web_menu SET number = ? WHERE (id = ?)', $_number, $id);

		clearCache('mainmenu');

		return $data;
	}
	
	private function mainmenuGetPages() {

		$data = new TObject();

		$data->pages = Array();

		$qr = DB::execute('SELECT id, caption, url_name, status FROM web_pages');
		while ($row = DB::fetch_object($qr)) {
			$page = new TObject();
			$page->id = $row->id;
			$page->caption = $row->caption;
			$page->status = $row->status;
			$page->href = ($row->id == 1 ? '/' : '/pages/' . $row->url_name);
			$data->pages[] = $page;
		}

		return $data;
	}
}
?>
<?php
/**
 * OrdersModel
 */
class OrdersModel extends EttonModel {
	
	public $resources_module = Array(
		Array('css' => 1, 'head' => 1, 'href' => '/core/libs/jquery-ui-1.10.4/jquery-ui-1.10.4.custom.css'),
		Array('css' => 1, 'head' => 1, 'href' => '/catalog/etton/orders/orders.css')
	);
	
	public $orders = Array();
	public $order = null;
	
	public function __construct() {
		parent::__construct();
		$this->entity->type = 'order';
		$this->entity_orders = true; // для подсветки активного пн.меню
		$this->orders = new TObject(); // TODO: auto ORM + подсистема прав доступа на сущности/операции
		$this->orders->items = null;
	}
	
	/**
	 * Получить список всех заказов, отсортированный по заданному полю
	 * @param string $sorting
	 * @return Array
	 */
	public function getFullList($sorting_field, $sorting_dir) {
		
		if (! $sorting_field) $sorting_field = '1'; // id
		
		$qr = DB::execute('
			SELECT
				o.id, o.number, o.createdate, o.customer,
				(SELECT sum(s.quantity) FROM ett_orders_spec s WHERE (s.order_id = o.id)) AS spec_count
			FROM ett_orders o
			WHERE (o.user_id = ?)
			' . ($sorting_field ? 'ORDER BY ' . $sorting_field . ' ' . $sorting_dir : '') . ' LIMIT 0, 1000;', User::id()); // TODO: "LIMIT 0, 1000" => pagination
		
		$items = Array();
		while ($item = DB::fetch_object($qr)) {
			$item->createdate = Utils::getDateStr($item->createdate);
			$item->customer = (string)$item->customer;
			$item->spec_count = (int)$item->spec_count;
			$items[] = $item;
		}
				
		return $items;
	}
	
	/**
	 * Проверить допустимость сортировки по заданному полю
	 * @param string $sorting
	 * @return string/null
	 */
	public function normalizeSortingField($sorting) {
		if (in_array($sorting, Array('createdate', 'spec_count'))) {
			return $sorting;
		} else {
			return null;
		}
	}
	
	/**
	 * Проверить параметр сортировки
	 * @param string $dir
	 * @return string
	 */
	public function normalizeSortingDir($dir) {
		if (($dir == 'asc') || ($dir == 'desc')) return $dir;
		return null;
	}
	
	/**
	 * Получить основные данные заказа и спецификацию
	 * @param int $id
	 * @return object
	 */
	public function getOrder($id) {
		
		$qr = DB::execute('
			SELECT o.id, o.number, o.createdate, o.customer
			FROM ett_orders o
			WHERE (o.id = ?) AND (o.user_id = ?)', $id, User::id());
		
		$detail = DB::fetch_object($qr);
		
		if ($detail) {
			$detail->customer = (string)$detail->customer;
			$detail->spec = $this->getSpec($id);
		}
		
		return $detail;
	}
	
	/**
	 * Получить спецификацию заказа
	 * @param int $order_id
	 * @return Array
	 */
	public function getSpec($order_id) {
		
		$items = Array();
		
		$qr = DB::execute('
			SELECT s.id, s.position_id, p.name name, t.id subtype_id, t.name subtype_name, s.quantity
			FROM ett_orders o
				LEFT JOIN ett_orders_spec s ON (s.order_id = o.id)
				LEFT JOIN ett_positions p ON (p.id = s.position_id)
				LEFT JOIN ett_positions_subtypes t ON (t.id = s.subtype_id)
			WHERE (o.id = ?) AND (o.user_id = ?) AND (s.id IS NOT NULL) ORDER BY s.id', $order_id, User::id());
		
		while ($item = DB::fetch_object($qr)) $items[] = $item;
			
		return $items;
	}
	
	/**
	 * Добавление/изменение основных данных заказа
	 * @param int $id
	 * @param string $number
	 * @return object
	 */
	public function saveOrder($id, $number, $customer, $spec) {
		
		$result = new TObject();
		
		$isAdd = ! $id;
		$isEdit = ! $isAdd;
		
		// Проверка прав доступа
		if ($isEdit) {
			$qr = DB::execute('SELECT o.id FROM ett_orders o WHERE (o.id = ?) AND (o.user_id = ?);', $id, User::id());
			$v = (int)DB::fetch_val($qr);
			if (! $v) {
				$result->error = 'Ошибка! Заказ с ИД='.$id.' не найден! Возможно он был удалён или у вас нет к нему доступа!';
				return $result;
			}
		}
		
		// Проверка номера
		$qr = DB::execute('SELECT id FROM ett_orders WHERE (id <> ?) AND (number = ?);', $id, $number);
		if (DB::rowsCount($qr)) {
			$result->error = 'Заказ с таким номером уже существует в системе!';
			// TODO: Автоматизировать на уровне ядра: использовать единый формат выдачи списка полей, которые не прошли валидацию на сервере
			/*$result->errors = new TObject(); ...
			$result->errors->fields = Array({ name: 'number', message: 'Недопустимый номер!'}. {...});*/
			return $result;
		}
		
		$success = false;
		
		if (! $id) {
			$id = DB::genID('ett_orders');
			$success = DB::execute('INSERT INTO ett_orders (id, user_id, number, createdate, customer) VALUES (?, ?, ?, Now(), ?);', $id, User::id(), $number, $customer);
		} else {
			$success = DB::execute('UPDATE ett_orders SET number = ?, customer = ? WHERE (id = ?);', $number, $customer, $id);
		}
		
		if ($success) clearCache('etton_orders_list'.User::id());
		
		// Сохранение спецификации
		if ($success) {
			
			$order_id = $id;
			
			$ids = Array(); // Для удаление отсутствующих позиций спецификации
			foreach ($spec as &$item) {
				$spec_id = $this->saveSpec($order_id, $item);
				if (! $spec_id) {
					$success = false;
					break;
				}
				$ids[] = $spec_id;
			}
			unset($item);
			
			if (($success) && ($isEdit)) {
				$success = $this->deleteMissingSpec($order_id, $ids);
			}
		}
		
		if ($success) {
			$result->id = $id;
			$result->success = 'Заказ успешно '.($isAdd ? 'добавлен!' : 'сохранён!');
		} else {
			$result->error = 'Возникли ошибка при сохранении заказа!';
		}
		
		return $result;
	}
	
	private function saveSpec($order_id, $item) {
		$spec_id = null;
		if (! $item['id']) {
			$spec_id = DB::genID('ett_orders_spec');
			if (! DB::execute('INSERT INTO ett_orders_spec (id, order_id, position_id, subtype_id, quantity) VALUES (?,?,?,?,?);', $spec_id, $order_id, $item['position_id'], $item['subtype_id'], (int)$item['quantity'])) {
				$spec_id = null;
			}
		} else {
			$spec_id = $item['id'];
			DB::execute('UPDATE ett_orders_spec SET quantity = ? WHERE (id = ?);', (int)$item['quantity'], $spec_id);
		}
		return $spec_id;
	}
	
	private function deleteMissingSpec($order_id, $ids) {
		$ids_sql = implode(',', $ids);
		if ($ids_sql) {
			return DB::execute('DELETE FROM ett_orders_spec WHERE (order_id = ?) AND (id NOT IN ('.$ids_sql.'));', $order_id);
		} else {
			return DB::execute('DELETE FROM ett_orders_spec WHERE (order_id = ?);', $order_id);
		}
	}
	
	/**
	 * Удалить заказ со спецификацией
	 * @param int $id
	 * @return object
	 */
	public function deleteOrder($id) {
		
		$result = new TObject();
		
		// Проверка прав доступа
		$qr = DB::execute('SELECT o.id FROM ett_orders o WHERE (o.id = ?) AND (o.user_id = ?);', $id, User::id());
		$v = (int)DB::fetch_val($qr);
		if (! $v) {
			$result->error = 'Ошибка! Заказ с ИД='.$id.' не найден! Возможно он был удалён или у вас нет к нему доступа!';
			return $result;
		}
		
		$qr = DB::execute('DELETE FROM ett_orders_spec WHERE (order_id = ?);', $id);
		$spec_count = ($qr ? DB::affectedRows() : 0);
		
		$qr = DB::execute('DELETE FROM ett_orders WHERE (id = ?);', $id);
				
		$result->success = ($qr == true ? 'Заказ успешно удалён (spec_count='.$spec_count.')!': 'Ошибка при удалении заказа!');
		$result->spec_count = $spec_count;
		
		return $result;
	}
}
?>
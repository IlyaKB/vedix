<?php

include_once(HD_ETTON . 'positions\positions.model.php'); // use some methods

/**
 * OrdersController
 */
class OrdersController extends EttonController {
	
	const TTL = 60; // TODO: 60 // Кешируем на 60 сек.
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
		
		$this->model = new OrdersModel(); // TODO: auto ORM + подсистема прав доступа на сущности/операции
	}
	
	public function run() {
		
		parent::run();
		
		if (Request::AJAX()) { // AJAX/POST
			
			if (! User::id()) { // // TODO: auto ORM + подсистема прав доступа на сущности/операции
				Request::stopError('Сессия недействительна! У вас нет прав доступа!');
			}
			
			$method = Utils::_POST('method');
			
			switch (Request::JX()) {
				case 'list': {
					EXIT(json_encode($this->getOrders())); // TODO: JSON-RPC 2.0
				}
				case 'detail': {
					switch ($method) {
						case 'get': {
							// TODO: начать "транзакцию", что бы другой пользователь не мог удалять/изменять этот заказ
							EXIT(json_encode($this->getOrder())); // TODO: JSON-RPC 2.0
						}
						case 'save': {
							// TODO: commit "транзакции"
							EXIT(json_encode($this->saveOrder())); // TODO: JSON-RPC 2.0
						}
						case 'delete': {
							EXIT(json_encode($this->deleteOrder())); // TODO: JSON-RPC 2.0
						}
						default: {
							Request::stop('Получен не поддерживаемый метод ('.$method.')!', Request::HH_ACCESSERROR);
						}
					}
				}
				case 'positions': {
					switch ($method) {
						case 'get': {
							EXIT(json_encode($this->getPositions())); // TODO: JSON-RPC 2.0
						}
					}
				}
				case 'subtypes': {
					switch ($method) {
						case 'get': {
							EXIT(json_encode($this->getSubtypes())); // TODO: JSON-RPC 2.0
						}
					}
				}
				default: {
					Request::stop('Получена не поддерживаемая команда ('.Request::JX().')!', Request::HH_ACCESSERROR);
				}
			}
		} else { // GET
			
			if (User::id()) {
				// По умолчанию получаем список всех заказов
				$this->getOrders();
			}
		}

		return TRUE;
	}
	
	/**
	 * Получить список заказов (из кеша/БД)
	 */
	private function getOrders() {
		
		$sorting_field = $this->model->normalizeSortingField(Utils::_POST('sortfield'));
		$sorting_dir = $this->model->normalizeSortingDir(Utils::_POST('sortdir'));
		$key = ($sorting_field ? $sorting_field . '-' . $sorting_dir : 'default');
		
		$result = new TObject();
		
		// Пытаемся получить данные из файлового кеша / БД
		if (! getCache('etton_orders_list'.User::id(), $key, $this->model->orders)) {
			$this->model->orders = $this->model->getFullList($sorting_field, $sorting_dir);
			setCache('etton_orders_list'.User::id(), $key, $this->model->orders, self::TTL);
		}
		
		$result->orders = $this->model->orders;
		$result->success = true;
		
		return $result;
	}
	
	private function getOrder() {
		
		$id = (int)Utils::_POST('id');
		if (! $id) Request::stop('Error: get order with ID=0!', Request::HH_LOGICERROR);
		
		$result = new TObject();
		$result->order = $this->model->getOrder($id);
		if (! $result->order) {
			$result->error = 'Ошибка! Заказ с ИД='.$id.' не найден! Возможно он был удалён или у вас нет к нему доступа!';
		} else {
			$result->success = true;
		}
		return $result;
	}
	
	private function saveOrder() {
		
		// TODO: JSON-RPC parser: $order = Utils::_POST('order'); if (! is_object($order)) $order = new TObject(); // На входе должен быть Javascript-object
		$id = (int)Utils::_POST('id');
		$number = Utils::_POST('number');
		$customer = Utils::_POST('customer');
		
		$spec = Utils::_POST('spec');
		if (! is_array($spec)) $spec = Array();
		foreach ($spec as &$item) {
			$item['id'] = (int)$item['id'];
			if ($item['id'] < 0) $item['id'] = null;
			$item['position_id'] = (int)$item['position_id'];
			if ($item['position_id'] <= 0) Request::stop('Error: position_id=0!', Request::HH_LOGICERROR);
			$item['subtype_id'] = Utils::getAElement($item, 'subtype_id', null);
			//if (! $item['subtype_id']) $item['subtype_id'] = null;
		}
		unset($item);
		
		$result = $this->model->saveOrder($id, $number, $customer, $spec);
		if (property_exists($result, 'success')) $this->clearListCache();
		
		return $result;
	}
	
	private function deleteOrder() {
		$id = (int)Utils::_POST('id');
		if (! $id) Request::stop('Error: delete order with ID=0!', Request::HH_LOGICERROR);
		$result = $this->model->deleteOrder($id);
		if (property_exists($result, 'success')) $this->clearListCache();
		return $result;
	}
	
	private function clearListCache() {
		// TODO: сделать на уровне ядра удаление кеша только по первой части ключа: clearCache('etton_orders_list'.User::id());
		clearCache('etton_orders_list'.User::id(), 'default');
		clearCache('etton_orders_list'.User::id(), 'createdate-asc');
		clearCache('etton_orders_list'.User::id(), 'createdate-desc');
		clearCache('etton_orders_list'.User::id(), 'spec_count-asc');
		clearCache('etton_orders_list'.User::id(), 'spec_count-desc');
	}
	
	private function getPositions() {
		$result = new TObject();
		$modelPositions = new PositionsModel();
		$result->positions = $modelPositions->getList();
		$result->success = true;
		return $result;
	}
	
	private function getSubtypes() {
		
		$position_id = (int)Utils::_POST('position_id');
		if (! $position_id) Request::stop('Error: get subtypes list for $position_id=0!', Request::HH_LOGICERROR);
		
		$result = new TObject();
		$modelPositions = new PositionsModel();
		$result->subtypes = $modelPositions->getSubtypes($position_id);
		$result->success = true;
		return $result;
	}
}
?>
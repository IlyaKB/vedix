<?php
namespace VediX;

abstract class Controller {
	
	/**
	 * Модель (создается автоматически при подключении компонента методом Page::addComponent или Page::addComponentAndRun)
	 * @var object Model object
	 */
	public $model = null;
	
	
	public function __construct( $params = Array() ) {	}
	
	/**
	 * Присоединить к модели новые данные
	 * @param object $modelData Данные
	 * @return void
	 */
	protected function addData($modelData) {
		if (! $modelData) return;
		foreach ($modelData as $key => $val) {
			//$t = gettype($val);
			$this->model->$key = $val;
		}
	}
	
	abstract protected function run();
}
?>
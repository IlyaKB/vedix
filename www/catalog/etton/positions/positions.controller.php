<?php
namespace VediX;

/**
 * PositionsController
 */
final class PositionsController extends EttonController {
	
	const TTL = 1;
	const CELL_COUNT = 12;
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
		
		$this->model = new PositionsModel();
	}
	
	public function run() {
		
		parent::run();
		
		if (Request::AJAX()) {
			
			if (! User::id()) {
				Request::stopError('Сессия недействительна! У вас нет прав доступа!');
			}
			
			switch (Request::JX()) {
				//case 'cmd1': EXIT(EntonUtils::cmd1('par1', 'par2', $post_url_name));
				default: {
					Request::stop('Получена не поддерживаемая команда ('.Request::JX().')!', Request::HH_ACCESSERROR);
				}
			}
		} else {
			
			if (User::id()) {
				
				$model = $this->model;
				//$positions = $model->positions;

				// ...
			}
		}

		return TRUE;
	}
}
?>
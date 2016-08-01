<?php
namespace VediX;

/**
 * IndexController
 */
final class IndexController extends EttonController {
	
	const TTL = 1;
	const CELL_COUNT = 12;
	
	public function __construct( $params = Array() ) {
		parent::__construct( $params );
		
		$this->model = new IndexModel();
	}
	
	public function run() {
		
		parent::run();
		
		if (Request::AJAX()) {
			switch (Request::JX()) {
				//case 'cmd1': EXIT(EntonUtils::cmd1('par1', 'par2', $post_url_name));
				default: {
					Request::stop('Получена не поддерживаемая команда ('.Request::JX().')!', Request::HH_ACCESSERROR);
				}
			}
		} else {
			//
		}

		return TRUE;
	}
}
?>
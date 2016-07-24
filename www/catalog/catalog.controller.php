<?php

/**
 * Главный контроллер скина "Catalog"
 */
class CatalogController extends PageController {
	
	public function __construct( $params = Array() ) {
		
		parent::__construct( $params );
		
		$this->model = new CatalogModel();
	}
	
	public function run() {
		
		parent::run();
		
		//$_SESSION['authorized'] = false;
		
		if (Request::AJAX()) {
			switch (Request::JX()) {
				case 'authenticate': {
					if (! Utils::getAElement($_SESSION, 'authorized', false)) {
						include_once(HD_CORE . 'authenticate.php');
						EXIT(json_encode( authenticate() ));
					} else {
						$result = new TObject;
						$result->error = 'You have already signed in!';
						EXIT(json_encode($result));
					}
				}
				case 'logout': EXIT(json_encode($this->_logout()));
			}
		} else {
			if (isset($_GET['logout'])) {
				$this->_logout();
				if (preg_match('/etton/', Utils::_SERVER('HTTP_REFERER'))) { header('Location: /etton/'); EXIT(); } // TODO: DEL
				header('Location: /');
			}
		}
	}
	
	private function _logout() {
		$result = new TObject();
		if (User::logout()) {
			$result->success = true;
		} else {
			$result->error = 'Error on logout!';
		}
		return $result;
	}
}
?>
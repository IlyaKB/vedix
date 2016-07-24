<?php

/**
 * Модель
 */
class Model {
	
	static private $instance = null;
	
	static public function getInstance() {
		if (! self::$instance) {
			self::$instance = new self ( );
		}
		return self::$instance;
	}
}
?>
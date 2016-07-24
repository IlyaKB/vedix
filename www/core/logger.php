<?php

/**
 * Logger
 */
class Logger {
	
	static private $instance;
	
	static public function getInstance() {
		if (! self::$instance) {
			self::$instance = new self ( );
		}
		return self::$instance;
	}
	
	function __construct() {
		
		if (self::$instance) {
			Request::stop('Ошибка! Экземпляр класса Logger может быть создан только один раз! Для использования следует использовать метод Logger::getInstance()!');
		}
		if (!self::$instance) self::$instance = $this;
	}
	
	public static function append(Exception $e, $message = '') {
		
		$message = $message ? $message .= "\n" . $e->getMessage () : $e->getMessage ();
		$message .= "Stack trace:\n" . $e->getTraceAsString (). "\n".'Error in module "' . $e->getFile () . '" (' . $e->getLine ().')';
			
		file_put_contents ( HD_ROOT .'logs/exceptions.log', date ( 'd.m.Y H:i:s' ) . ', ERROR:' ."\n".$message."\n\n", FILE_APPEND );

	}
}
?>
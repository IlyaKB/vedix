<?php
header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: VediX');

date_default_timezone_set('Europe/Moscow');

ini_set('display_errors', 1); error_reporting(E_ALL); # Debug. TODO: off

include_once('bootstrap.php');

$_PAGE = new VediX\Page();
$_PAGE->run();
$_PAGE->display();

?>
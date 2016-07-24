<?php
header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: VediX');

date_default_timezone_set('Europe/Moscow');

//exit(md5('1'));
//setlocale(LC_ALL, 'ru_RU.utf-8', 'rus_RUS.utf-8', 'ru_RU.utf8');
//setlocale(LC_ALL, 'ru_RU.CP1251');

ini_set('display_errors', 1); error_reporting(E_ALL); # Debug. TODO: off

include_once('bootstrap.php');

$_PAGE = new Page();
$_PAGE->run();
$_PAGE->display();

?>
<?php
namespace VediX;

if (! defined('CACHE_DEFAULT_TTL')) define('CACHE_DEFAULT_TTL', 60);

/**
 * Получить данные по namespace и ключу
 * @param string $namespace Используется при массовом удалении ключей одного типа
 * @param mixed $key Параметры ключа // TODO: сделать составным, т.е. задавать строкой "part1.part2.part3...."
 * @param mixed &$value Значение
 */
function getCache($namespace, $key, &$value) {
	
	if (Request::DEMO()) return false;
	
	$filename = getFilename($namespace, $key);
	if (! $filename) return false;
	if (! is_file($filename . '.dat')) return false;
	$time = CACHE_DEFAULT_TTL;
	if (is_file($filename . '.t')) $time = (int) file_get_contents($filename . '.t');
	if ( (isset($_REQUEST['clearcache'])) || (time() - filemtime($filename . '.dat') > $time) ) {
		unlink($filename . '.dat');
		@unlink($filename . '.t');
		return false;
	}
	$value = json_decode(file_get_contents($filename . '.dat'));
	return true;
}

function setCache($namespace, $key, &$value, $time = CACHE_DEFAULT_TTL) {
	
	if (Request::DEMO()) return true;
	
	$filename = getFilename($namespace, $key);
	if (! $filename) return false;
	file_put_contents($filename . '.dat', json_encode($value));
	file_put_contents($filename . '.t', $time);
	return true;
}

function clearCache($namespace, $key = null) {
	
	if (Request::DEMO()) return;
	
	$filename = getFilename($namespace, $key);
	if (! $filename) return false;
	if (is_file($filename . '.dat')) unlink($filename . '.dat');
	if (is_file($filename . '.t')) unlink($filename . '.t');
}

function getFilename($namespace, $key) {
	if ( ($key) && (! is_string($key)) ) $key = md5(json_encode($key));
	if (strlen($key) != 32) $key = md5($key);
	return HD_CACHE . $namespace . '_' . $key;
}
?>
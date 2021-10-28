<?php
/**
 * Kerio APIs Client Library for PHP - Config Example.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../doc/resources/class/JsonConfig.php');

/* Application details */
$name = 'Sample Application';
$vendor = 'Kerio Technologies s.r.o.';
$version = '1.4.0.234';

try {
	/* Get shared config file */
	$config = new JsonConfig(dirname(__FILE__) . '/servers.json');
	$server = $config->get();

	$hostname = $server['hostname'];
	$username = $server['username'];
	$password = $server['password'];

	/* Check if config file is empty */
	if(empty($hostname) || empty($username) || empty($password)) {
 		die('Before running code examples please, <a target="_top" href="../../tools/config-assistant/">setup</a> your login credentials.');
	}
}
catch (Exception $e) {
	die($e->getMessage());
}




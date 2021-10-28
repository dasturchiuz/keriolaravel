<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Get basic server info.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Get basic server info';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Need some basic info about your server? Get version, platform, uptime, active connections.<br />Super easy and quick.');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get product info */
	$result = $api->sendRequest('Server.getProductInfo');
	$productInfo = $result['info'];
	printf('Product info: %s %s build %d<br>Platform: %s<br>',
		$productInfo['productName'],
		$productInfo['version'],
		$productInfo['buildNumber'],
		$productInfo['osName']);

	/* Get server uptime */
	$result = $api->sendRequest('Statistics.get');
	$uptime = $result['statistics']['uptime'];
	printf('Uptime: %d day(s) %d hour(s) %d minute(s)<br>',
		$uptime['days'],
		$uptime['hours'],
		$uptime['minutes']);

	/* Get active connections, web sessions */
	$param_query = array(
		'query' => array(
			'orderBy' => array(array(
   				'columnName' => 'proto',
   				'direction' => 'Asc'
   			))
		)
	);
	/* Display only total items count */
	$result = $api->sendRequest('Server.getConnections', $param_query);
	printf('Active connections: %d<br>', $result['totalItems']);
	$result = $api->sendRequest('Server.getWebSessions', $param_query);
	printf('Active web sessions: %d<br>', $result['totalItems']);
}
catch (KerioApiException $error) {

	/* Catch possible errors */
	$html->printError($error->getMessage());
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

$html->printFooter();

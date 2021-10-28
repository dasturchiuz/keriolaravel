<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Display occupied storage from server statistics using Google API.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Display occupied storage from server statistics';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Are you aware of occupied storage on your server? Get a quick preview using Google Charts.');

/* Main application */
try {

	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get server statistics */
	$statistics = $api->getServerStatistics();

	/* Get server storage info */
	$occupied = $statistics['storage']['occupied']['value'];
	$free = $statistics['storage']['total']['value'] - $occupied;

	/* Prepare data for charts */
	$data = array(
		'free' => round ($free / 1024, 1),
		'occupied' => round($occupied / 1024, 1)
	);

	print '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
	print '<script type="text/javascript" src="../resources/googleChartsApi.js"></script>';
	print '<script type="text/javascript">';
	printf('setData(%s)', json_encode($data));
	print '</script>';
	print '<div id="statistics">Loading data, please wait...</div>';
}

/* Catch possible errors */
catch (Exception $error) {
	$html->printError($error->getMessage());
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

$html->printFooter();

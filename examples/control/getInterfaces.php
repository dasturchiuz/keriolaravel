<?php

/**
 * Kerio Control - Sample Application.
 *
 * STATUS: In progress, might change in the future
 *
 * This api is currently under development.
 * The api is not intended for stable use yet.
 * Functionality might not be fully verified, documented, or even supported.
 *
 * Please note that changes can be made without further notice.
 *
 * @copyright    Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/../../src/KerioControlApi.php');

$name = 'Example: Get all users with any administrator rights';
$api = new KerioControlApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Basic product info about license expiration and size, software version and connectivity (network).');

/* Main application */
try {
	$session = $api->login($hostname, $username, $password);
	
	$params = array(
		"sortByGroup" => true,
		"query" => array(
			"start" => 0,
			"limit" => -1,
			"orderBy" => array(array(
				"columnName" => "name",
				"direction" => "Asc"
			))
		)
	);
	$response = $api->sendRequest("Interfaces.get", $params);

	/* Print network info */
	print '<table width="100%">'
		. '<tr>'
		. '<th class="control">Interface</th>'
		. '<th class="control">IP/Mask</th>'
		. '</tr>';

	foreach ($response['list'] as $interface) {
		print '<tr>';
		printf('<td>%s</td><td>%s</td>', $interface['name'], $interface['ip']);
		print '</tr>';
	}
	print '</table>';

} catch (KerioApiException $error) {

	/* Catch possible errors */
	print $error->getMessage();
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

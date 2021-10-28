<?php
/**
 * Kerio Operator - Sample Application.
 *
 * Get active calls.
 *
 * STATUS: In progress, might change in the future
 *
 * This api is currently under development.
 * The api is not intended for stable use yet.
 * Functionality might not be fully verified, documented, or even supported.
 *
 * Please note that changes can be made without further notice.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../../src/KerioOperatorApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/../config.php');

$name = 'Example: Get active calls';
$api = new KerioOperatorApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Quick way to see who is calling who.');

/* Main application */
try {

	/* Login */
	$session = $api->login($hostname, $username, $password);
	
	/* Set query */
	$params = array(
		"query" => array(
			"fields" => array(
				"from",
				"to",
				"status",
				"answered_duration",
				"call_duration"
			),
			"limit" => -1,
			"orderBy" => array(array(
				"columnName" => "status",
				"direction" => "Asc"
			))
		)
	);

	$result = $api->sendRequest('Status.getCalls', $params);

	if (1 <= $result['totalItems']) {
		print '<table class="withBorder">';
		print '  <thead>';
		print '    <th class="operator withBorder">From</th>';
		print '    <th class="operator withBorder">To</th>';
		print '    <th class="operator withBorder">Duration</th>';
		print '  </thead>';
		print '  <tbody>';
		
		foreach ($result['calls'] as $record) {
			printf('<tr><td>%d</td><td>%d</td><td>%d seconds</td></tr>', $record['FROM']['NUMBER'], $record['TO']['NUMBER'], $record['CALL_DURATION']);
		}
		print '  </tbody>';
		print '</table>';
	}
	else {
		print '<i>No active call.</i>';
	}
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

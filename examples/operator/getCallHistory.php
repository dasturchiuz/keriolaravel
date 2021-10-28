<?php
/**
 * Kerio Operator - Sample Application.
 *
 * Get last calls.
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

$name = 'Example: Get last 50 calls';
$api = new KerioOperatorApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Display the most recently dialed numbers.');

/* Main application */
try {

	/* Login */
	$session = $api->login($hostname, $username, $password);
	
	/* Last calls limit */
	$limit = 50;

	/* Set query */
	$params = array(
		"query" => array(
			"orderBy" => array(array(
				"columnName" => "TIMESTAMP",
				"direction" => "Desc"
			)),
			"start" => 0,
			"limit" => $limit
		)
	);

	$result = $api->sendRequest('CallHistory.get', $params);

	print '<table>';
	print '  <thead>';
	print '    <th class="operator">From</th>';
	print '    <th class="operator">To</th>';
	print '    <th class="operator">Duration</th>';
	print '    <th class="operator">Date</th>';
	print '  </thead>';
	print '  <tbody>';
	
	foreach ($result['callHistory'] as $record) {
		$date = date("Y-m-d H:i:s", $record['TIMESTAMP']);
		printf('<tr><td>%d</td><td>%d</td><td>%d seconds</td><td>%s</td></tr>', $record['FROM_NUM'], $record['TO_NUM'], $record['CALL_DURATION'], $date);
	}
	print '  </tbody>';
	print '</table>';

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

<?php
/**
 * Kerio Control - Sample Application.
 *
 * Get used memory.
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
require_once(dirname(__FILE__) . '/../../src/KerioControlApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/../config.php');

$name = 'Example: Get used memory';
$api = new KerioControlApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('This graph shows memory consumption history (last two hours).');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get data */
	$params = array('type' => 'HistogramTwoHours');
	$result = $api->sendRequest('SystemHealth.get', $params);
	$usage = $result['data']['memory'];

	/* Plot chart */
	print '<img src="https://chart.googleapis.com/chart?cht=lc&chd=t:';

	$count = count($usage);
	foreach (array_reverse($usage) as $value){
		$count--;
		print ($value > 0) ? round($value, 2) : '0';
		if ($count > 0) print ",";
	}

	print '&chxt=y&chs=800x250" alt="Memory consumption" />';

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


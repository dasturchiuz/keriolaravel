<?php
/**
 * Kerio Operator - Sample Application.
 *
 * Get CPU load info.
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

$name = 'Example: Get CPU load info';
$api = new KerioOperatorApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('This graph shows CPU load history (last two hours).');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get data */
	$params = array('histogramType' => 'HistogramTwoHours');
	$result = $api->sendRequest('SystemHealth.get', $params);
	$usage = $result['systemHealth']['cpu'];

	/* Plot chart */
	print  '<img src="https://chart.googleapis.com/chart?cht=lc&chd=t:';
	
	$count = count($usage);
	foreach (array_reverse($usage) as $value){
		$count--;
		print ($value > 0) ? round($value, 2) : '0';
		if ($count > 0) print ",";
	}
	
	print '&chxt=y&chs=800x250&" alt="CPU load" />';
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

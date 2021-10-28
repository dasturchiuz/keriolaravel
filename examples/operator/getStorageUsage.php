<?php
/**
 * Kerio Operator - Sample Application.
 *
 * Plot storage statistic chart.
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

$name = 'Example: Get storage usage';
$api = new KerioOperatorApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Here you can see how the used hard disk space is occupied by each data types.');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get data */
		$params = array(
		"histogramType" => 'HistogramOneDay'
	);
	$systemHealth = $api->sendRequest('SystemHealth.get', $params);
	$storage = $api->sendRequest('Storage.get');
	
	/* Prepare data for charts */
	$logs = round($storage['storageData']['0']['size']/1024/1024, 1);
	$crashes = round($storage['storageData']['1']['size']/1024/1024, 1);
	$dumps = round($storage['storageData']['2']['size']/1024/1024, 1);
	$voicemails = round($storage['storageData']['3']['size']/1024/1024, 1);
	$records = round($storage['storageData']['4']['size']/1024/1024, 1);
	$audiofiles = round($storage['storageData']['5']['size']/1024/1024, 1);
	$config = round($storage['storageData']['6']['size']/1024/1024, 1);

	/* Count overal */
	$health = $systemHealth['systemHealth'];
	$disktotal = round($health['diskTotal']/1024/1024, 1);
	$diskfree = round($health['diskFree']/1024/1024, 1);
	$diskused = $disktotal - $diskfree;
	
	/* Other */
	$other = $diskused - ($logs + $crashes + $dumps + $voicemails + $records + $audiofiles + $config);
	$total = $logs+$crashes+$dumps+$voicemails+$records+$audiofiles+$config+$other;

	/* Plot chart */
	$labels = 'Logs|Crashes|Pkt. dumps|Voicemail|Rec. calls|Audio|Config|Other';
	$values = $logs/$total .','
			. $crashes/$total . ','
			. $dumps/$total . ','
			. $voicemails/$total . ','
			. $records/$total . ','
			. $audiofiles/$total . ','
			. $config/$total . ','
			. $other/$total;

	print '<table>';

	print '<tr>';
	print '<td rowspan="11" >';
	printf('<img src="https://chart.googleapis.com/chart?cht=p3&chd=t:%s&chs=500x250&chl=%s" alt="Storage statistic" />', $values, $labels);
	print '</td>';
	print '<th class="operator">Name:</th>';
	print '<th class="operator">Value:</th>';
	print '</tr>';	
	
	printf('<tr><td>Log files</td><td>%g MB</td></tr>', $logs);
	printf('<tr><td>Crash dumps</td><td>%g MB</td></tr>', $crashes);
	printf('<tr><td>Packet dumps</td><td>%g MB</td></tr>', $dumps);
	printf('<tr><td>Voicemail</td><td>%g MB</td></tr>', $voicemails);
	printf('<tr><td>Recorded calls</td><td>%g MB</td></tr>', $records);
	printf('<tr><td>Audio files</td><td>%g MB</td></tr>', $audiofiles);
	printf('<tr><td>Configuration</td><td>%g MB</td></tr>', $config);
	printf('<tr><td>Other files</td><td>%g MB</td></tr>', $other);
	printf('<tr><td>Total</td><td>%g MB</td></tr>', $diskused);
	
	print '<tr><td></td><td></td></table>';
	
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

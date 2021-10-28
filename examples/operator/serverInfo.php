<?php
/**
 * Kerio Operator - Sample Application.
 *
 * Get server info.
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

$name = 'Example: Server info';
$api = new KerioOperatorApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Basic product info about license expiration and size, software version and connectivity (network).');

/* Main application */
try {

	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get data */
	$constants = $api->getConstants();
	$network = $api->sendRequest('Network.get');
	$license = $api->sendRequest('ProductRegistration.getFullStatus');

	$productExpires = ($license['status']['expirations']['0']['isUnlimited'])
		? 'Never'
		: sprintf('%s - in %d day(s)', date('Y-m-d', $license['status']['expirations']['0']['date']), $license['status']['expirations']['0']['remainingDays']);

	$maintenanceExpires = ($license['status']['expirations']['1']['isUnlimited'])
		? 'Never'
		: sprintf('%s - in %d day(s)', date('Y-m-d', $license['status']['expirations']['1']['date']), $license['status']['expirations']['1']['remainingDays']);
	
	/* Print server info */
	$html->printTable(array(
		'Product version:' => $constants['product']['SERVER_SOFTWARE'],
		'Hostname:' => $network['network']['HOSTNAME'],
		'Running on:' => $constants['product']['SERVER_OS'],
		'&nbsp;' => '&nbsp;',
		'Number of registered users:' =>  $license['status']['users'],
		'Product license expires:' => $productExpires,
		'Software maintenance expires:' => $maintenanceExpires
	));

	print '<br />';

	/* Print network info */
	print '<table width="100%">'
		. '<tr>'
		. '<td>Interface</td>'
		. '<td>Description</td>'
		. '<td>IP/Mask</td>'
		. '<td>Gateway</td>'
		. '</tr>';

	foreach ($network['network']['INTERFACES'] as $interface) {
		print '<tr>';

		printf('<td>%s</td><td>%s</td>', $interface['NAME'], $interface['DESCRIPTION']);

		print '<td>';
		foreach ($interface['STATUS']['IP_ADDRESSES'] as $ip) {
			$isDhcp = (isset($ip['ENABLE_DHCP'])) ? 'DHCP' : '';
			printf('%s/%s %s', $ip['IP_ADDRESS'], $ip['MASK'], $isDhcp);
			if (count(1 < $interface['STATUS']['IP_ADDRESSES'])) print '<br />';
		}
		print '</td>';
		
		printf('<td>%s</td>', $interface['STATUS']['GATEWAY']);

		print '</tr>';
	}
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

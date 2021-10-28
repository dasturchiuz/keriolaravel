<?php
/**
 * Kerio Operator - Sample Application.
 *
 * Get dial plan.
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

$name = 'Example: Get dial plan';
$api = new KerioOperatorApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Simple address book with all the numbers of extensions, conferences, services. At a single spot.');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get Value */
	$params = array(
		'query' => array(
			'orderBy' => array(array(
				'columnName' => 'TEL_NUM',
				'direction' => 'Asc'
			))
		),
		'onlyExtensions' => FALSE
	);
	$result = $api->sendRequest('Server.getInternalDirectoryList', $params);

	/* Print messages */
	if ($result['totalItems'] > 0) {
		print '<table>';
		print '  <thead>';
		print '    <th class="operator">Line</th>';
		print '    <th class="operator">Description</th>';
		print '  </thead>';
		print '  <tbody>';

		foreach ($result['numberList'] as $ext) {
			
			switch ($ext['EXTENSION_TYPE_ID']) {
				case 1: $type = 'Line'; break;
				case 2: $type = 'Interface'; break;
				case 3: $type = 'Outbound Route'; break;
				case 4: $type = 'Conference'; break;
				case 5: $type = 'Call Queue'; break;
				case 6: $type = 'Ivr'; break;
				case 7: $type = 'Voicemail'; break;
				case 8: $type = 'Emergency Number'; break;
				case 9: $type = 'Ring Group'; break;
				case 10: $type = 'Echo Service'; break;
				case 11: $type = 'Music Service'; break;
				case 12: $type = 'External Service'; break;
				case 13: $type = 'Dial by Name Service'; break;
				case 14: $type = 'Time Service'; break;
				case 15: $type = 'Record Audio Service'; break;
				case 16: $type = 'Pickup Service'; break;
				case 17: $type = 'Call Pickup Service'; break;
				case 18: $type = 'Speed Dial'; break;
				case 19: $type = 'Call Parking Service'; break;
				case 20: $type = 'Line Group'; break;
				default: $type = 'Unknown'; break;
			}
			$description = (empty($ext['DESCRIPTION'])) ? '(Not assigned)' : $ext['DESCRIPTION'];
			if (1 < $ext['EXTENSION_TYPE_ID']) $description = $type;
			 
			printf('<tr><td>%s</td><td>%s</td></tr>',
				$ext['TEL_NUM'], $description);
		}

		print '  </tbody>';
		print '</table>';
	}
	else {
		print '<i>Dial plan is empty.</i>';
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

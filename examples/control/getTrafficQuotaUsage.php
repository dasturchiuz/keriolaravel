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

$name = 'Example: User quota usage';
$api = new KerioControlApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('How much?');

/* Main application */
try {
	$session = $api->login($hostname, $username, $password);
	
	$params = array(
		'query' => array(
			'combining' => 'And',
			'orderBy' => array(array(
				'columnName' => 'userName',
				'direction' => 'Asc'
			))
		),
		'domainId' => 'local' // local database
	);
	$users = $api->sendRequest('Users.get', $params);

	$quota = array('daily', 'weekly', 'monthly');

	/* Result table */
	print '<table>'
		. '<thead>'
		. '  <th class="control">userName</th>';
	
	/* Generate dynamic right table */
	foreach ($quota as $interval) {
		printf('<th class="control">%s</th>', $interval);
	}
	
	print '</thead>'
		. '<tbody>';

	/* Display user rights */
	foreach ($users['list'] as $user) {

		print '<tr>';
		printf('<td>%s</td>', $user['credentials']['userName']);

		foreach ($quota as $interval) {
			$value = $user['data']['quota'][$interval]['limit']['value'];
			$units = $user['data']['quota'][$interval]['limit']['units'];
			
			printf('<td align="center">%d %s</td>', $value, $units);
		}
		print '</tr>';
	}
	print '</tbody></table>';
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

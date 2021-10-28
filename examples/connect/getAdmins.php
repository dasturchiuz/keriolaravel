<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Get all users with any administrator rights.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Get all users with any administrator rights';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('One of frequent daily routine. Oh gosh! Are there really so many users with admin rights?');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get domain list */
	$fields = array('name', 'id');
	$domainList = $api->getDomains($fields);

	foreach ($domainList as $domain) {
		printf('<h2>Domain %s</h2>', $domain['name']);
		
		/* Get user list */
		$fields = array(
			'loginName',
			'fullName',
			'role'
		);
		$userList = $api->getUsers($fields, $domain['id']);

		/* Empty domain check */
		if (count($userList) <= 0) {
			print '<i>No user in this domain.</i>';
			continue;
		}

		/* Result table */
		print '<table>'
				. '<thead>'
				. '  <th class="connect">userName</th>';
		
		/* Generate dynamic right table */
		foreach ($userList as $user) {
			foreach ($user['role'] as $role => $value) {
				printf('<th class="connect">%s</th>', $role);
			}
			break;
		}
		
		print '</thead>'
				. '<tbody>';
		
		/* Display user rights */
		foreach ($userList as $user) {
			$username = ($user['fullName']) ? sprintf('%s - <span class="inline-note">%s</span>', $user['loginName'], $user['fullName']) : $user['loginName'];
			printf('<td>%s</td>', $username);
		
			foreach ($user['role'] as $role => $value) {
				if (is_string($value)) {
					$inputEl = $value;
				}
				else {
					$checked = ($value) ? 'checked' : '';
					$inputEl = sprintf('<input type="checkbox" title="%s" disabled %s>', $value, $checked);
				}
				printf('<td align="center">%s</td>', $inputEl);
			}
			print '</tr>';
		}
		print '</tbody></table>';
	}
	print '<p><b>Legend:</b><br>'
		. '<ul>'
		. '<li>FullAdmin - Whole server read/write</li>'
		. '<li>AccountAdmin - Manage accounts in own domain</li>'
		. '<li>Auditor - Whole server read only</li>'
		. '<li>UserRole - No admin access</li>'
		. '</ul></p>';

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


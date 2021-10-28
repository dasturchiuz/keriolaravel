<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Get users with quota exceeded 90%.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Get users with quota exceeded 90%';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('If a user\'s mailbox exceeds its storage quota, he will not be able to receive emails. List these users.');

$output = '';

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get list of available constants */
	$constants = $api->getConstants();

	/* Get list of available domains */
	$fields = array('id', 'name');
	$domainList = $api->getDomains($fields);

	/* Go throw all domains */
	foreach ($domainList as $domain) {

		/* Get list of users for each domain */
		$fields = array('loginName', 'fullName', 'diskSizeLimit', 'consumedSize');
		$domainId = $domain['id'];

		$userList = $api->getUsers($fields, $domainId);

		/* Go throw all available users, check their quotas */
		foreach ($userList as $user) {

			/* See Users.idl for user's structure */
			if (true == $user['diskSizeLimit']['isActive']) {

				$percent = round($html->getSizeInBytes($user['consumedSize'], $constants) / $html->getSizeInBytes($user['diskSizeLimit']['limit'], $constants) * 100, 1);

				if ($percent >= 90) {

					$output .= '<b>' . $user['fullName'] . '</b> (' . $user['loginName'] . '@' . $domain['name'] . ')';

					if ($percent >= 100) {
						$output = '<span style="color: red">' . $output . '</span>';
					}

					print $output . ' consumes <b>' . $percent . '% </b>of his disk quota.<br>';
				}
			}
		}
	}

	if (0 === strlen($output)) {
		print '<i>No user exceeds his quota.</i>';
	}
}

/* Catch possible errors */
catch (KerioApiException $error) {
	$html->printError($error->getMessage());
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

$html->printFooter();

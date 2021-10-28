<?php
/**
 * Samepage - Sample Application.
 *
 * Invite users from a file.
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
require_once(dirname(__FILE__) . '/../../src/SamepageApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/../config.php');

$name = 'Example: Invite users from a file';
$api = new SamepageApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Invite more users at once without a clicking one by one. Just create a text file with email address on each line.');

/* Main application */
try {

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if ($_FILES['filename']['type'] != 'text/plain') throw new KerioApiException('Please select a text file.');
		
		$invitations = array();
		$content = explode("\n", file_get_contents($_FILES['filename']['tmp_name']));
		
		foreach ($content as $email) {
			/* Validate email address */
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;
			array_push($invitations, $email);
		}
		
		/* Send invitations */
		if (count($invitations) > 0) {

			/* Login */
			$session = $api->login($hostname, $username, $password);

			foreach ($invitations as $email) {
				try {
					$params = array('email' => $email);
					$result = $api->sendRequest('Invitation.inviteUser', $params);
						
					$html->printSuccess(sprintf('Request to %s has been sent.', $email));
				}
				catch (KerioApiException $error) {
					$html->printError($error->getMessage());
				}
			}
		}
		else {
			throw new KerioApiException('Sorry. This file doesn\'t contain any valid email address.');
		}
	}

}
catch (KerioApiException $error) {
	/* Catch possible errors */
	$html->printError($error->getMessage());
}

/* Print upload form */
$html->printUploadForm();

/* Logout */
if (isset($session)) $api->logout();

/* HTML Footer */
$html->printFooter();

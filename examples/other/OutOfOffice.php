<?php
/**
 * Kerio Connect WebMail 2.0 - Sample Application
 *
 * Set Out of Office.
 *
 * STATUS: In progress, might change in the future
 *
 * This example is currently under development.
 * The example is not intended for stable use yet.
 * Functionality might not be fully verified, documented, or even supported.
 *
 * Please note that changes can be made without further notice.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../../src/KerioConnectApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/../config.php');

$name = 'Example: Set Out of Office';
$api = new KerioConnectApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Setting your vacation message has been never easier.');

/* Main application */
try {
	/* Set component Kerio Connect Client */
	$api->setComponentClient();

	/* Login */
	$session = $api->login($hostname, $username, $password);
	
	/* Print user details */
	$whoAmI = $api->sendRequest('Session.whoAmI');
	$html->printInfo(sprintf('Note: You are logged in as %s.', $whoAmI['userDetails']['loginName']));

	/* Form posted? */
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$enabled = (bool) (isset($_POST['enabled']));
		$message = htmlspecialchars($_POST['message']);

		try {
			$params = array(
				'settings' => array(
					'isEnabled' => $enabled,
					'isTimeRangeEnabled' => FALSE,
//					'isTimeRangeEnabled' => TRUE,
//					'timeRangeStart' => '20120509T080000+0200',
//					'timeRangeEnd' => '20120510T080000+0200',
					'text' => $message
				)
			);
			$api->sendRequest('Session.setOutOfOffice', $params);
			
			$status= ($enabled) ? 'enabled' : 'disabled';
			$html->printSuccess(sprintf('Your Out of Office is %s now.', $status));
		}
		catch (KerioApiException $error) {
			/* Catch possible errors */
			$html->printError($error->getMessage());
		}
	}

	/* Get OOO settings */	
	$settings = $api->sendRequest('Session.getOutOfOffice');
	$html->printOutOfOfficeForm($settings['settings']);
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

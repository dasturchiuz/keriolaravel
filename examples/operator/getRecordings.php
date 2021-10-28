<?php
/**
 * Kerio Operator - Sample Application.
 *
 * Get recoreded calls.
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

define('DOWNLOAD_DIR', dirname(__FILE__));

$name = 'Example: Get recorded calls';

$api = new KerioOperatorApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Get recorded calls.');

/* Main application */
try {

	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* A POST request for download? */
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$id = htmlspecialchars($_POST['id']);
		
		$result = $api->sendRequest('Recordings.downloadAudioContent', array("id" => $id));
		$fileName = $result['fileDownload']['name'];
		$api->downloadFile($result['fileDownload']['url'], DOWNLOAD_DIR, $fileName);
		$html->printSuccess(sprintf('File <a href="%s">%s</a> has been downloaded successfully.', $fileName, $fileName));
	}
	
	/* Set query */
	$params = array(
		"query" => array(
			"fields" => array(
				"ID",
				"NAME",
				"EXTENSION",
				"CALLER_ID",
				"DURATION",
				"STARTED"
			),
			"start" => 0,
			"limit" => -1,
			"orderBy" => array(array(
				"columnName" => "STARTED",
				"direction" => "Desc"
			))
		)
	);

	$calls = $api->sendRequest('Recordings.get', $params);

	if (1 <= $calls['totalItems']) {
		$html->printRecordedCalls($calls);
	}
	else {
		print '<i>No recordings.</i>';
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

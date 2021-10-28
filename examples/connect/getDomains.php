<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Get all domains.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Get all domains';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Print all domains available on the server.');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get domains */
	$params = array(
		"query" => array(
			"fields" => array(
				"id",
				"name",
				"description",
				"aliasList",
				"userMaxCount",
				"outgoingMessageLimit",
				"isPrimary",
				"renameInfo",
				"forwardingOptions",
				"kerberosRealm",
				"winNtName",
				"ipAddressBind",
				"pamRealm",
				"keepForRecovery",
				"isDistributed"),
			"start" => 0,
			"limit" => -1,
			"orderBy" => array(array(
				"columnName" => "name",
				"direction" => "Asc"
			))
		)
	);
	$result = $api->sendRequest('Domains.get', $params);

	$domainsList = $result['list'];

	/* Print available domains */
	print '<h2>Available domains</h2>';
	foreach ($domainsList as $domain) {
		printf('%s<br>', $domain['name']);
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

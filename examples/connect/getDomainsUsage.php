<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Get usage of all domains.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Get domains usage';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Need to monitor how much space each domain really uses?');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);
	
	/* Get list of available constants */
	$constants = $api->getConstants();

	/* Get all domains */
	$params = array(
		"query" => array(
			"fields" => array(
				"id",
				"name"),
			"start" => 0,
			"limit" => -1,
			"orderBy" => array(array(
				"columnName" => "name",
				"direction" => "Asc"
			))
		)
	);
	$domains = $api->sendRequest('Domains.get', $params);

	foreach ($domains['list'] as $domain) {
		$domainId	= $domain['id'];
		$domainName	= $domain['name'];

		/* Get users in the domain */
		$params = array(
			"query" => array(
				"fields" => array("consumedSize"),
				"start" => 0,
				"limit" => -1,
			),
			"domainId" => $domainId
		);
		$users = $api->sendRequest('Users.get', $params);

		/* Print available users */
		$usage = 0;
		if ($users['totalItems'] > 0) {
			foreach ($users['list'] as $user) {
				$usage += $user['consumedSize']['value'];
			}
		}
		printf('Usage of domain %s: %dMB<br>', $domainName, $usage / 1024);
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

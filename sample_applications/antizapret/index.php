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
require_once(dirname(__FILE__) . '/../../examples/config.php');
require_once(dirname(__FILE__) . '/../../examples/class/ControlApiHelper.php');
require_once(dirname(__FILE__) . '/class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/../../src/KerioControlApi.php');

$name = 'AntiZapret';
$api = new KerioControlApi($name, $vendor, $version);
$apiHelper = new ControlApiHelper($api);

/* Group name */
$groupName = 'AntiZapret';

/* AntiZapret API */
$antizapretApi = 'http://api.antizapret.info/group.php?data=domain';

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('This script uses the AntiZapret.info service to automatically update the URL Group on server.');

/* Main application */
print '<form action="" method="POST"><input type="submit" value="Update"></form>';
/* A POST request? */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	try {
		/* Login */
		$session = $api->login($hostname, $username, $password);

		/* Get all records from AntiZapret */
		$antizapret = file_get_contents($antizapretApi);
		$blacklist = explode("\n", $antizapret);
	
		/* Delete old records */
		$apiHelper->removeUrlGroup($groupName);
		$html->printNote("Old records have been removed.");
		
		/* Add new records */
		$apiHelper->addHostToUrlGroupFromArray($groupName, $blacklist);
		$html->printNote("New records have been added.");
		
		/* Apply config */
		$apiHelper->applyUrlGroups();
		$html->printSuccess("Configuration has been applied.");		
	}
	catch (KerioApiException $error) {

		/* Catch possible errors */
		$html->printError($error->getMessage());
	}
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

$html->printFooter();

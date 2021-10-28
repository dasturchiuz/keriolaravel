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
require_once(dirname(__FILE__) . '/../class/ControlApiHelper.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/../../src/KerioControlApi.php');

$name = 'Example: Setup Custom HTTP Rule';
$api = new KerioControlApi($name, $vendor, $version);
$apiHelper = new ControlApiHelper($api);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Create an HTTP rule which disables example.com');

/* Main application */
try {
	$session = $api->login($hostname, $username, $password);

	$apiHelper->createDenyHttpRule("Access to example.com", "example.com");
	$html->printSuccess("Access to example.com has been denied.");
}
catch (KerioApiException $error) {
	$html->printError($error->getMessage());
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

$html->printFooter();

<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Get fullname of the authenticated user.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Get fullname of the authenticated user';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('A <i>Hello World</i>-like example. See how to login, send a query and receive a response.');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Who am I ? */
	$result = $api->sendRequest('Session.whoAmI');
	$fullname = $result['userDetails']['fullName'];
	printf('Logged in as <b>%s</b>.', $fullname);
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

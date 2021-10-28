<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Show debugging options.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Show debugging options';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Call <i>setDebug(TRUE)</i> whenever you want to see what data is sent and recieved or just to print your own debugging messages.');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Enable debug */
	$api->setDebug(TRUE);

	/* Who am I ? */
	$result = $api->sendRequest('Session.whoAmI');
	$api->debug('Called Session.whoAmI');
	$api->debug(sprintf('Response data:<pre>%s</pre>', print_r($result, TRUE)));

	$fullname = $result['userDetails']['fullName'];
	$api->debug(sprintf('Value of variable $fullname is %s', $fullname));

	$html->printSuccess(sprintf('Logged in as <b>%s</b>', $fullname));

	/* Disable debug */
	$api->setDebug(FALSE);
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
?>

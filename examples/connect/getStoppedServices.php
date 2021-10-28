<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Show stopped services.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Show stopped services';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('When any service is not running, your mailserver may not work properly. That you need to know as soon as possible.');

$areServicesRunning = true;

/* Main application */
try {

	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get all available services */
	$serviceList = $api->getServices();

	/* Go throw the services */
	foreach ($serviceList as $service) {

		/* Check whether service is not running */
		if (false == $service['isRunning']) {

			/* Check available ports for given service */
			$ports = '';

			foreach ($service['listeners'] as $listener) {
				$ports .= $listener['port'] . ', ';
			}

			/* Cut last comma */
			$ports = substr($ports, 0, -2);

			printf('<span style="color: red">%s</span> is stopped on port(s) %s<br>', $service['name'], $ports);

			$areServicesRunning = false;
		}
	}

	if (true == $areServicesRunning) {
		print '<i>All services are running.</i>';
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

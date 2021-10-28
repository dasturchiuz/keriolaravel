<?php
/**
 * Administration API for Kerio Connect - Create Domain.
 *
 * Create domain.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Create domain';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Creating new domain takes just a while. Enter a name - done!');

/* Main application */
try {

	/* A POST request? */
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		
		$domain = htmlspecialchars($_POST['domain']);
		
		/* Form passed? */
		if ($domain) {
			try {
				/* Login */
				$session = $api->login($hostname, $username, $password);
				/* Create domain */
				$api->createDomain($domain);
				$html->printSuccess('Domain has been added successfully.');
			}
			catch (KerioApiException $error) {
				$html->printError($error->getMessage());
			}
		}
		else {
			$html->printError('Please provide all details.');
		}
	}

	/* HTML form */
	$html->printCreateDomainForm();
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

<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Add alias.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Create alias';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('One of the <i>real</i> applications. Why should I login to Web Administration everytime I need to add just a single alias?');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get domain list */
	$fields = array('id', 'name');
	$domainList = $api->getDomains($fields);

	/* A POST request? */
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$domain = htmlspecialchars($_POST['domain']);
		$email  = htmlspecialchars($_POST['email']);
		$alias  = htmlspecialchars($_POST['alias']);

		/* Form passed? */
		if ($domain && $alias && $email) {
			try {
				$api->createAlias($domain, $alias, $email);
				$html->printSuccess('Alias has been added successfully.');
			}
			catch (KerioApiException $error) {
				$html->printError($error->getMessage());
			}
		}
		else {
			$html->printError('Please provide all details.');
		}
	}
	/* Print add alias form */
	$html->printAddAliasForm($domainList);
}
catch (KerioApiException $error) {
	$html->printError($error->getMessage());
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

$html->printFooter();

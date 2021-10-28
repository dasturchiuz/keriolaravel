<?php
/**
 * Administration API for Kerio Connect - Create User.
 *
 * Create user.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Create user';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Do you want to create a new user without all the details? Enter a username, password - done!');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get domain list */
	$fields = array('id', 'name');
	$domainList = $api->getDomains($fields);
	
	/* A POST request? */
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		
		$user  = htmlspecialchars($_POST['username']);
		$password  = htmlspecialchars($_POST['password']);
		$domainId = htmlspecialchars($_POST['domainId']);
		
		/* Form passed? */
		if ($user && $password && $domainId) {
			try {
				$api->createUser($domainId, $user, $password);
				$html->printSuccess('User has been added successfully.');
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
	$randomPassword = $api->genRandomPassword(10);
	$html->printCreateUserForm($domainList, $randomPassword);
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

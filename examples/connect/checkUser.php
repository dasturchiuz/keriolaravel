<?php
/**
 * Administration API for Kerio Connect - Check User.
 *
 * Check user availability.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Check user availability';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Do you want to create a new user and check whether the address is not in conflict with another entity?');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get domain list */
	$fields = array('id', 'name');
	$domainList = $api->getDomains($fields);
	
	/* A POST request? */
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		
		$newUser  = htmlspecialchars($_POST['username']);
		$domainId = htmlspecialchars($_POST['domainId']);
		
		/* Valid inputs */
		if ($newUser && $domainId) {
			/* Check for entity */
			$params = array('addresses' => array($newUser), 'updatedEntity' => array('kind' => 'EntityUser'), 'domainId' => $domainId);
			$result = $api->sendRequest('Server.findEntityByEmail', $params);
			
			/* Print conflicts */
			if (count($result['entities'])) {
				
				foreach ($result['entities'] as $entity) {
					if ($entity['isPattern']) continue;

					$usedBy = strtolower(substr($entity['kind'], 6));
					$html->printError(sprintf('Username %s is not available. Already used as %s entity.', $newUser, $usedBy));
				}
			}
			else {
				$html->printSuccess(sprintf('Username %s is available. Do you want to <a href="createUser.php">create</a> it?', $newUser));
			}
		}
		else {
			$html->printError('Missing username and/or domain.');
		}
	}

	/* HTML form */
	$html->printCheckUserForm($domainList);
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

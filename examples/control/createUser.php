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
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/../../src/KerioControlApi.php');
require_once(dirname(__FILE__) . '/../class/ControlApiHelper.php');

$name = 'Example: Create a user';
$api = new KerioControlApi($name, $vendor, $version);
$apiHelper = new ControlApiHelper($api);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('An example how to create and modify a user.');

/* Main application */
try {
	$session = $api->login($hostname, $username, $password);
	
	$userData = array(
		"data" => array(
			"rights" => array(
				"readConfig" => false,
				"writeConfig" => false,
				"unlockRule" => false,
				"dialRasConnection" => false,
				"connectVpn" => false,
				"useP2p" => false
			),
			"quota" => array(
				"daily" => array(
					"enabled" => false,
					"type" => "QuotaBoth",
					"limit" => array(
						"value" => 0,
						"units" => "GigaBytes"
					)
				),
				"weekly" => array(
					"enabled" => false,
					"type" => "QuotaBoth",
					"limit" => array(
						"value" => 0,
						"units" => "GigaBytes"
					)
				),
				"monthly" => array(
					"enabled" => false,
					"type" => "QuotaBoth",
					"limit" => array(
						"value" => 0,
						"units" => "GigaBytes"
					)
				),
				"blockTraffic" => false,
				"notifyUser" => false
			),
			"wwwFilter" => array(
				"javaApplet" => false,
				"embedObject" => false,
				"script" => false,
				"popup" => false,
				"referer" => false
			),
			"language" => "detect"
		),
		"credentials" => array(
			"userName" => "jdoe",
			"password" => "Secret",
			"passwordChanged" => true
		),
		"fullName" => "",
		"description" => "",
		"email" => "",
		"authType" => "Internal",
		"useTemplate" => true,
		"adEnabled" => true,
		"localEnabled" => true,
		"groups" => [

		],
		"autoLogin" => array(
			"addresses" => array(
				"enabled" => false,
				"value" => null
			),
			"addressGroup" => array(
				"enabled" => false,
				"id" => null
			),
			"macAddresses" => array(
				"enabled" => false,
				"value" => null
			)
		),
		"vpnAddress" => array(
			"enabled" => false,
			"value" => null
		)
	);

	$params = array(
		"users" => array($userData),
		"domainId" => "local"
	);
	
	/* Create user */
	$result = $api->sendRequest("Users.create", $params);
	$userId = $result['result'][0]['id'];
	
	/* Update user */
	$userData["fullName"] = "John Doe";
	$params = array(
		"userIds" => array($userId),
		"details" => $userData,
		"domainId" => "local"
	);
	$api->sendRequest("Users.set", $params);

	$timestamp = $apiHelper->getConfigTimestamp();
	$apiHelper->confirmConfig($timestamp);
	
	$html->printSuccess("User created.");

} catch (KerioApiException $error) {

	/* Catch possible errors */
	print $html->printError($error->getMessage());
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

$html->printFooter();

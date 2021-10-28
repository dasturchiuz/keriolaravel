<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Get all users.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Get all users';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Need to overview all users in each domain? No problem.');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get all domains */
	$params = array(
		"query" => array(
			"fields" => array(
				"id",
				"name",
				"description",
				"aliasList",
				"userMaxCount",
				"outgoingMessageLimit",
				"isPrimary",
				"renameInfo",
				"forwardingOptions",
				"kerberosRealm",
				"winNtName",
				"ipAddressBind",
				"pamRealm",
				"keepForRecovery",
				"isDistributed"),
			"start" => 0,
			"limit" => -1,
			"orderBy" => array(array(
				"columnName" => "name",
				"direction" => "Asc"
			))
		)
	);
	$result = $api->sendRequest('Domains.get', $params);
	$domainList = $result['list'];

	foreach ($domainList as $domain) {
		$domainId	= $domain['id'];
		$domainName	= $domain['name'];

		/* Get users in the domain */
		$params = array(
			"query" => array(
				"fields" => array(
					"id",
					"loginName",
					"fullName",
					"description",
					"authType",
					"itemSource",
					"isEnabled",
					"isPasswordReversible",
					"emailAddresses",
					"emailForwarding",
					"userGroups",
					"role",
					"itemLimit",
					"diskSizeLimit",
					"consumedItems",
					"consumedSize",
					"hasDomainRestriction",
					"outMessageLimit",
					"effectiveRole",
					"homeServer",
					"migration",
					"lastLoginInfo"),
				"start" => 0,
				"limit" => -1,
				"orderBy" => array(array(
					"columnName" => "loginName",
					"direction" => "Asc"
				)),
			),
			"domainId" => $domainId
		);
		$result = $api->sendRequest('Users.get', $params);
		$userList = $result['list'];

		/* Print available users */
		printf('<h2>Users in %s</h2>', $domainName);
		if ($result['totalItems'] > 0) {
			foreach ($userList as $user) {
				printf('%s@%s<br>', $user['loginName'], $domainName);
			}
		}
		else {
			print '<i>No users in this domain.</i>';
		}
	}
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

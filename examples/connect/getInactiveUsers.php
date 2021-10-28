<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Get inactive users.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Get inactive users';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Too many user accounts? How many of them are inactive? Cut your license expenses.');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get all domains */
	$params = array(
		'query' => array(
			'fields' => array(
				'id',
				'name'),
			'start' => 0,
			'limit' => -1,
			'orderBy' => array(array(
				'columnName' => 'name',
				'direction' => 'Asc'
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
			'query' => array(
				'fields' => array(
					'id',
					'loginName',
					'fullName',
					'lastLoginInfo'),
				'conditions' => array(array(
					'fieldName' => 'homeServer',
					'comparator' => 'Eq',
					'value' => $domainId
				)),
				'start' => 0,
				'limit' => -1,
				'orderBy' => array(array(
					'columnName' => 'loginName',
					'direction' => 'Asc'
				)),
			),
			'domainId' => $domainId
		);
		$result = $api->sendRequest('Users.get', $params);
		$userList = $result['list'];

		/* Print inactive users */
		printf('<h2>Never logged users in %s</h2>', $domainName);
		if ($result['totalItems'] > 0) {
			foreach ($userList as $user) {
				if ($user['lastLoginInfo']['dateTime']) continue;
				printf('%s@%s<br>', $user['loginName'], $domainName);
			}
		}
		else {
			print '<i>No inactive users in this domain.</i>';
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

<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Get top 10 mailbox usage in each domain.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Get top 10 mailbox usage in each domain';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Getting often out of space? Check this out how to easily get a quick overview of top 10 mailboxes.');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get domain list */
	$fields = array('id', 'name');
	$domainList = $api->getDomains($fields);

	foreach ($domainList as $domain) {
		printf('<h2>Domain %s</h2>', $domain['name']);

		/* Get user list */
		$query_params = array(
			'query' => array(
				'fields' => array(
					'loginName',
					'fullName',
					'consumedSize'
				),
				'orderBy' => array(array(
					'columnName' => 'consumedSize',
					'direction' => 'Desc'
				)),
				'start' => 0,
				'limit' => 10
			),
			'domainId' => $domain['id']
		);
		$userList = $api->sendRequest('Users.get', $query_params);

		if ($userList['totalItems'] > 0) {
			foreach ($userList['list'] as $user) {
				$username = sprintf('%s@%s', $user['loginName'], $domain['name']);
				$fullname = $user['fullName'];
				$usage = $user['consumedSize']['value'];
				$units = $user['consumedSize']['units'];
				printf('%s (%s) consumes %d %s.<br>', $username, $fullname, $usage, $units);
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

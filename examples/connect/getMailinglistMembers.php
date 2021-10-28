<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Get mailing list members.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Get mailing list members';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('So many mailing lists, so many members in. Have you ever wondered how to make a summary?');

/* Main application */
try {
	/* Login */
	$login = $api->login($hostname, $username, $password);

	/* Get domains */
	$fields = array('id', 'name');
	$domainsList = $api->getDomains($fields);

	foreach ($domainsList as $domain) {
		printf('<h2>Domain: %s</h2>', $domain['name']);

		$fields = array('id', 'name');
		$mailingListList = $api->getMailingLists($fields, $domain['id']);

		/* Go throw all available mailing lists */
		if (count($mailingListList) > 0) {
			foreach ($mailingListList as $mailingList) {
				printf('<b>%s</b>', $mailingList['name']);
				print '<ul>';

				/* Prepare params for Users.get request. */
				$userParams = array(
			    	'query' => array(
						'fields' => array('id')
					),
			        'mlId' => $mailingList['id']
				);

				/* Get list of users which belong to the group */
				$fields = array('emailAddress', 'fullName', 'kind');
				$userList = $api->getMlUserList($fields, $mailingList['id']);

				/* Go throw all users in the mailing list */
				if (count($userList) > 0) {
					foreach ($userList as $user) {
						$fullname = ($user['fullName'])
							? sprintf('(%s)', $user['fullName'])
							: '';
	
						($user['kind'] == 'Moderator')
							? printf('<li><b>%s %s</b></li>', $user['emailAddress'], $fullname)
							: printf('<li>%s %s</li>', $user['emailAddress'], $fullname);
					} //foreach user
				}
				else {
					print '<i>No user in this list.</i>';
				}
				print '</ul>';
			} //foreach mailingList
		}
		else {
			print '<i>No mailing lists in this domain.</i>';
		}
	} //foreach domain
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

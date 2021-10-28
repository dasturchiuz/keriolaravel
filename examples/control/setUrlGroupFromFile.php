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
require_once(dirname(__FILE__) . '/../class/ControlApiHelper.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/../../src/KerioControlApi.php');

$name = 'Example: Setup URL group from file';
$api = new KerioControlApi($name, $vendor, $version);
$apiHelper = new ControlApiHelper($api);

/* Group name
 * This is the group where we will synchronize IP addresses stored in blacklist.txt
 */
$groupName = 'Spammers - BlackList';

/* File with the IP addresses */
$file = '';

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo(sprintf('Imagine that. You maintain a custom list of forbidden addresses you block. This will help you to synchronize all the records stored in local file <i>%s</i> directly to the URL Group <i>%s</i> in Kerio Control. You can then easily use that group in traffic rules.', $file, $groupName));

/* Main application */
print '<form action="" method="POST"><input type="submit" value="Update"></form>';
/* A POST request? */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	try {
		/* Login */
		$session = $api->login($hostname, $username, $password);

		/* Get all records in the local file we will synchronize to server */
		$newList = $apiHelper->getBlacklistRecords($file);

		/* Get all records from server in the URL group */
		$result = $apiHelper->getUrlGroupList($groupName);
		$ipList = array();
		foreach ($result as $record) {
			if ($record['type'] == 'UrlChildGroup') continue;
			
			if ($record['groupName'] == $groupName) {
				array_push($ipList, $record['url']);
			}
		}

		/* Diff existing records with the new one to remove hosts not in blacklist.txt */
		$hostsToRemove = array_diff($ipList, $newList);
		$hostsToAdd = array_diff($newList, $ipList);

		/* Print hosts and add them */
		print '<h2>Added hosts</h2>';
		if (count($hostsToAdd) == 0) {
			print '<p><i>None.</i></p>';
		}
		else {
			print '<ul>';
			foreach ($hostsToAdd as $record) {
				try {
					printf('<li>%s</li>', $record);
					$apiHelper->addHostToUrlGroup($groupName, $record);
				} catch (KerioApiException $error) {
					$html->printError($error->getMessage());
				}
			}
			print '</ul>';
		}

		/* Print hosts and remove them */
		print '<h2>Removed hosts</h2>';
		if (count($hostsToRemove) == 0) {
			print '<p><i>None.</i></p>';
		}
		else {
			print '<ul>';
			foreach ($hostsToRemove as $record) {
				printf('<li>%s</li>', $record);
				$apiHelper->removeHostFromUrlGroup($groupName, $record);
			}
			print '</ul>';
		}
		
		$apiHelper->applyUrlGroups();
	}
	catch (KerioApiException $error) {

		/* Catch possible errors */
		$html->printError($error->getMessage());
	}
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

$html->printFooter();

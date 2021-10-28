<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Synchronize IP addresses from a local file to certain IP Address Group.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Synchronize Spam Blacklist';
$api = new MyApi($name, $vendor, $version);

/* Group name
 * This is the group where we will synchronize IP addresses stored in blacklist.txt
 */
$groupName = 'Spammers - BlackList';

/* File with the IP addresses */
$file = 'spamBlacklist.txt';

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo(sprintf('Imagine that. You maintain a custom blacklist of IP addresses you block due to excessive spam and lack of any legitimate emails.<br />This will synchronize all the records stored in local file <i>%s</i> directly to the IP Address Group <i>%s</i> in Kerio Connect.', $file, $groupName));

/* Main application */
print '<form action="" method="POST"><input type="submit" value="Update"></form>';
/* A POST request? */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	try {
		/* Login */
		$session = $api->login($hostname, $username, $password);

		/* Get all records in the local file we will synchronize to server */
		$newList = $api->getBlacklistRecords($file);

		/* Get all records from server in IP address group */
		$result = $api->getIpGroupList($groupName);
		$ipList = array();
		foreach ($result as $record) {
			array_push($ipList, $record['host']);
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
				printf('<li>%s</li>', $record);
				$api->addHostToIpGroup($groupName, $record);
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
				$api->removeHostFromIpGroup($groupName, $record);
			}
			print '</ul>';
		}
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

<?php
/**
 * Samepage.io - Sample import to File Library
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../../src/SamepageApi.php');
require_once(dirname(__FILE__) . '/class/SamepageClient.php');
require_once(dirname(__FILE__) . '/config.php');

/* Path to directory */
$directory = '';

$api = new SamepageApi('Import to File Library', 'Kerio Technologies s.r.o.', '1.4.0.234');

/* Main application */
try {
	/* File check */
	if (empty($directory))
		throw new KerioApiException('Cannot upload. Directory not set.');

	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Create a new page with file library inside and upload all files into it */
	$app = new SamepageClient($api);
	$pageId = $app->createPage(SamepageClient::ROOT, 'Testing page');

	$fileLibId = $app->createFileLibrary($pageId, 'My file library');
	$app->uploadFilesFromFolder($fileLibId, $directory);

	$app->createTextNoteComponent($pageId, 'Additional notes', '<p>This is page for my file, see it bellow.</p>');

	/* Done */
	printf("Files were uploaded. See https://%s/%s/#page-%d\n", $hostname, $api->getTenant(), $pageId);
} catch (KerioApiException $error) {
	print $error->getMessage() . "\n";
}

/* Logout */
if (isset($session)) {
	$api->logout();
}


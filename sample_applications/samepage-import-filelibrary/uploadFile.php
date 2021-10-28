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

/* /path/to/the/file.pdf */
$file = '';

$api = new SamepageApi('Import to File Library', 'Kerio Technologies s.r.o.', '1.4.0.234');

/* Main application */
try {
	/* File check */
	if (empty($file))
		throw new KerioApiException('Cannot upload. File not set.');

	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Create a new space, page, file library inside and upload a file into it, with some text info */
	$app = new SamepageClient($api);
	$pageId = $app->createPage(SamepageClient::ROOT, 'Testing page');

	$fileLibId = $app->createFileLibrary($pageId, 'My file library');
	$fileId = $app->upload($fileLibId, $file);

	$app->createTextNoteComponent($pageId, 'Additional notes', '<p>This is page for my file, see it bellow.</p>');

	/* Done */
	printf('File %s uploaded. See your <a href="https://%s//%s/#page-%d">page</a>.', basename($file), $hostname, $api->getTenant(), $pageId);
} catch (KerioApiException $error) {

	/* Catch possible errors */
	print $error->getMessage();
}

/* Logout */
if (isset($session)) {
	$api->logout();
}


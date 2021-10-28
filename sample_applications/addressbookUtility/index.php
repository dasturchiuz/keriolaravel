<?php
/**
 * Kerio Connect Client API - Sample Application.
 *
 * A tool for the Addressbook.app
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../../src/KerioConnectApi.php');
require_once(dirname(__FILE__) . '/class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/config.php');

$api = new KerioConnectApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo(sprintf('Logged to %s as a user %s.', $hostname, $username));

/* Main application */
try {

	/* Set component Kerio Connect Client */
	$api->setComponentClient();

	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* A POST request? */
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		try {
			/* Validate inputs */
			if (empty($_POST['folderId']) || empty($_POST['category'])) {
				throw new KerioApiException('Invalid params.');
			}
				
			$folderId = $_POST['folderId'];
			$categories = explode(',', $_POST['category']);

			/* Get contacts from particular folder selected by folderId */
			$params = array(
				'folderIds' => array($folderId),
				'query' => array(
					'start' => 0,
					'limit' => -1
				)
			);
			$contacts = $api->sendRequest('Contacts.get', $params);

			foreach ($contacts['list'] as $contact) {

				/* Merge existing contact's categories with the new ones from POST request */
				$newCategories = array_unique(array_merge($contact['categories'], $categories));
				$newCategories = implode(',', $newCategories);

				/* Update contacts */
				$params = array(
					'contacts' => array(array(
						'folderId' => $contact['folderId'],
						'id' => $contact['id'],
						'type' => 'ctContact',
						'categories' => array($newCategories)
					))
				);
				$result = $api->sendRequest('Contacts.set', $params);
			}
			$html->printSuccess(sprintf('Done. Categories have been updated.'));
		}
		catch (Exception $error) {
			print $html->printError($error->getMessage());
		}
	} // end of POST

	print '<p>Please, type categories you want apply to all contacts in the selected folder.</p>';
	print '<form action="" method="POST">';

	/* Print folders to form */
	$folders = $api->sendRequest('Folders.get');
	foreach ($folders['list'] as $folder) {
		if ($folder['type'] <> 'FContact') continue; // Process only contact folders
//		if ($folder['placeType'] <> 'FPlacePublic') continue; // Process only public folders folders

		/* Better HTML output */
		$placeType = ($folder['placeType'] == 'FPlacePublic') ? 'Public' : 'Personal';
		$folderAccess = $folder['access'];
		$radioType = ($folderAccess == 'FAccessReadOnly') ? 'disabled' : '';

		/* Print folder */
		printf('<input type="radio" name="folderId" value="%s" %s> %s (%s)<br>', $folder['id'], $radioType, $folder['name'], $placeType);
	}
	print '<p>Categories: <input type="text" name="category"> <i>(comma separated, e.g: Friends, Pub, School mates)</i></p>';
	print '<input type="submit" value="Set">';
	print '</form>';

}
catch (KerioApiException $error) {
	print $html->printError($error->getMessage());
}

/* Logout */
if (isset($session)) $api->logout();

$html->printFooter();

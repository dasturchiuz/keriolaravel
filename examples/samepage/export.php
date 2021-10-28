<?php
/**
 * Samepage - Sample Application.
 *
 * Invite users from a file.
 *
 * STATUS: In progress, might change in the future
 *
 * This api is currently under development.
 * The api is not intended for stable use yet.
 * Functionality might not be fully verified, documented, or even supported.
 *
 * Please note that changes can be made without further notice.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../../src/SamepageApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/../config.php');

$name = 'Example: Data Export';
$api = new SamepageApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Export data in a zip archive.');

$usage = memory_get_usage();
$html->printInfo(sprintf('PHP Note: Your allowed memory size is %d bytes', $usage));

/* Main application */
try {
	getAllSpaces();

	if(isset($_POST['export'])) {
		$export = preg_split('/:/', $_POST['export']);
		
		$id = $export[0];
		$name = $export[1];
		
		$name = (isset($name) && !empty($name)) ? $name : 'dataexport';
		
		$saveTo = sys_get_temp_dir();
		$saveAs = sprintf('%d-%s.zip', $id, $name);
		
		$api->downloadFile(sprintf('/%s/dataexport/%d', $api->getTenant(), $id), $saveTo, $saveAs);
		$html->printSuccess(sprintf('File has been saved to %s/%s', $saveTo, $saveAs));
	} //eof if action
	
}
catch(KerioApiException $e) {
	$html->printError($e->getMessage());
}
/* Logout */
if (isset($session)) $api->logout();

$html->printFooter();

/**
 */
function getAllSpaces() {
	global $api;
	global $hostname, $username, $password;
	global $session;
	
	/* Search */
	$params = array(
		'pid' => 'root',
		'id' => 'root',
		'sort' => array(array(
			'property' => 'name',
			'direction' => 'ASC'
		))
	);

	$session = $api->login($hostname, $username, $password);
	$result = $api->sendRequest('Items.getAllSpaces', $params);

	
	/* Parse response */
	if (count($result['items']) > 0) {
		
		print '<h2>Select space</h2>';
		print '<form action="" method="POST">';

		print '<select name="export">';
		foreach ($result['items'] as $item) {
			printf('<option value="%d:%s">%s</option>', $item['id'], $item['dashifiedName'], $item['name']);
		}
		print '</select>';
		
		print '<input type="submit" value="Export" />';
		print '</form>';
		
	}
	else{
		print 'Sorry. No space out there.';
	}
}


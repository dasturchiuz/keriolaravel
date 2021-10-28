<?php
/**
 * Administration API for Kerio Operator - Sample Application.
 * 
 * Backup Kerio Operator.
 * 
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
ini_set('memory_limit', '128M');
set_time_limit(1200);

require_once(dirname(__FILE__) . '/class/KerioOperator.php');
require_once(dirname(__FILE__) . '/class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/config.php');

$api = new KerioOperator($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);

try {
	$session = $api->login($hostname, $username, $password);

	$backupAllowed = TRUE;

	if ($api->getBackupState()) {
		$backupAllowed = FALSE;
		$html->printError('Another backup in progress, it is not possible to start a new one. Please wait.');
	}
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		$config = array(
				'SYSTEM_DATABASE'	=> (isset($_POST['SYSTEM_DATABASE'])) ? TRUE : FALSE,
				'VOICE_MAIL'		=> (isset($_POST['VOICE_MAIL'])) ? TRUE : FALSE,
				'SSL_CERTIFICATES'	=> (isset($_POST['SSL_CERTIFICATES'])) ? TRUE : FALSE,
				'SYSTEM_LOG'		=> (isset($_POST['SYSTEM_LOG'])) ? TRUE : FALSE,
				'CALL_LOG'			=> (isset($_POST['CALL_LOG'])) ? TRUE : FALSE,
				'LICENSE'			=> (isset($_POST['LICENSE'])) ? TRUE : FALSE
		);

		$api->createBackup($config);
		$api->downloadBackup($backupDir);
		$html->printSuccess('Backup finished successfully.');
	}

	$html->printBackupForm($backupAllowed);
	$html->printBackupFileList($backupDir, '*.bin', 10);
}
catch (Exception $error) {
	print $html->printError($error->getMessage());
}

if (isset($session)) {
	$api->logout();
}

$html->printFooter();

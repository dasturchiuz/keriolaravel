<?php
/**
 * Kerio APIs Client Library for PHP - Setup Assistant.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../../doc/resources/class/Html.php');

$html = new Html();
$html->setResources('../../doc/resources/');
$html->setBackpage('../../', '&lt;&lt; Back');

$html->printHeader('Setup Assistant');
print '<p>Before running Kerio APIs Client Library for PHP see how is your system ready. We check it for you.</p>';

/* Check PHP and modules */
print '<h2>1. Environment Check</h2>';
print '<p>Minimum PHP version required is 5.1.0 with OpenSSL, JSON and SQLite3 modules.</p>';

$phpOk = TRUE;
$modulesOk = TRUE;
if (version_compare(PHP_VERSION, '5.1.0', '<')) {
	$html->printWarning(sprintf('Your PHP version is <b>%s</b>. Please, upgrade your PHP installation.<br>For more information see <a target="_blank" href="http://php.net/downloads.php">http://php.net/downloads.php</a></p>', PHP_VERSION));
	$phpOk = FALSE;
}
if (FALSE === function_exists('openssl_open')) {
	$html->printWarning('Your PHP installation is missing OpenSSL support.<br>To configure OpenSSL in PHP, please edit your <b>php.ini</b> config file and enable row with php_openssl module, e.g. <b>extension=php_openssl.so</b><br>For more information see <a target="_blank" href="http://www.php.net/manual/en/openssl.installation.php">http://www.php.net/manual/en/openssl.installation.php</a></p>');
	$modulesOk = FALSE;
}
if (FALSE === function_exists('json_decode')) {
	$html->printWarning('Your PHP installation is missing JSON support.<br>To configure JSON in PHP, please edit your <b>php.ini</b> config file and enable row with php_json module, e.g. <b>extension=php_json.so</b><br>For more information see <a target="_blank" href="http://www.php.net/manual/en/json.installation.php">http://www.php.net/manual/en/json.installation.php</a></p>');
	$modulesOk = FALSE;
}
if (FALSE === extension_loaded('pdo_sqlite')) {
	$html->printWarning('Your PHP installation is missing PDO with SQLite support.<br>To configure PDO, please edit your <b>php.ini</b> config file and enable row with php_pdo_sqlite module, e.g. <b>extension=php_pdo_sqlite.dll</b><br>For more information see <a target="_blank" href="http://www.php.net/manual/en/pdo.installation.php">http://php.net/manual/en/pdo.installation.php</a>');
	$modulesOk = FALSE;
}

if (FALSE === $modulesOk) {
	$html->printNote('If you are not sure where your <b>php.ini</b> is located, see your <a href="../../doc/resources/phpinfo.php">phpinfo()</a> output and check your configuration file path.<br>E.g. on Linux, the path is usually <b>/etc/php5/apache2</b>');
}
if ($phpOk && $modulesOk) {
	$html->printSuccess(sprintf('Your PHP %s works with all the required modules properly.', PHP_VERSION));
}
else {
	$html->printNote('After configuration changes please restart the Apache HTTP server.');
}

/* Check files and directories */
print '<h2>2. Write Directory Check</h2>';
print '<p>Additional write access may be required, e.g. to store configuration data.</p>';

$writeOk = TRUE;
$displayFixMessage = TRUE;
$toCheck = array(
	'examples/servers.json' => 'File',
	'sample_applications/backupOperator/backup' => 'Directory',
	'sample_applications/mobileDevices/db' => 'Directory',
	'sample_applications/myServers/db' => 'Directory',
);
foreach ($toCheck as $entry => $type) {
	$path = realpath(dirname(__DIR__) . '/../') . '/' . $entry;

	if (FALSE === is_writable($path)) {

		/* If Linux */
		if (DIRECTORY_SEPARATOR == '/') {
			$chmodType = ($type == 'File') ? 666 : 777;
			$message = sprintf('chmod %d %s', $chmodType, $path);
			$fixMessage = 'To change write mode please, as the <span class="inline-note">root</span> open terminal and run:';
		}
		else {
			$message = $path;
			$fixMessage = 'Here is a list:';
		}

		/* On-time error description */
		($displayFixMessage)
			? $html->printNote(sprintf('Unfortunatelly some of them are not writeable. %s', $fixMessage))
			: '';
		$displayFixMessage = FALSE;
		
		/* Print toCheck */
		$html->printWarning($message);
		$writeOk = FALSE;
	}
}
if ($writeOk) {
	$html->printSuccess('All the required files are writeable.'); 
}

/* If all passed with no error */
if ($phpOk && $writeOk) {	
	print '<h2>Ready to go!</h2>';
	print '<p>Perfect! Now you can check out the code examples with no worries.</p>';
	$html->printSuccess('<a class="no-hover-success" href="../config-assistant"><img border="0" class="marker-icon" src="../../doc/resources/images/success.png"></a><a href="../config-assistant">Configure</a> your login credentials.');
}
else { 
	print '<h2>Before You Continue</h2>';
	print '<p>Your system needs some additional configuration, check the warnings above!</p>';
	$html->printWarning('<img class="marker-icon" src="../../doc/resources/images/warning.png">Please <a href="">refresh</a> this page when you fix it.');
}

$html->printFooter();


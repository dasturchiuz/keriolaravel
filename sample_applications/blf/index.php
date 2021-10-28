<?PHP
/**
 * Administration API for Kerio Operator - Sample Application.
 * 
 * Busy Lamp Field.
 * 
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../../src/KerioOperatorApi.php');
require_once(dirname(__FILE__) . '/class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/class/Extensions.php');
require_once(dirname(__FILE__) . '/class/Calls.php');
require_once(dirname(__FILE__) . '/config.php');

$api = new KerioOperatorApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader();

try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	$extensions = new Extensions($api);
	$calls = new Calls($api);
	$calls->get();
	
	/* Loop extensions */
	foreach ($extensions->get() as $extension) {

		/* Offline/not-registered lines */
		$status = ($extension->isRegistered()) ? 'online' : 'offline';

		/* Dialing/calling lines */
		$connection = $calls->isActive($extension->getSipNumber()); 

		if ($connection) {
			$status = ($connection->callStatusConnected == $connection->getStatus()) ? 'connected' : 'dialing';

			/* Get call direction */
			$direction = ($connection->getFrom() == $extension->getSipNumber())
				? 'to ' . $connection->getTo()
				: 'from ' . $connection->getFrom();

			/* Get active duraction when connected or display total call duration */
			$duration = ('connected' == $status)
				? gmdate("H:i:s", $connection->getActiveDuration())
				: gmdate("H:i:s", $connection->getCallDuration());
			$duration = 'Duration ' . $duration;

			/* Print output if dialing/calling */
			$html->printExtension($status, $extension->getUsername(), $extension->getSipNumber(), $direction, $duration);
		}
		else {
			/* Print output if online/offline */
			$html->printExtension($status, $extension->getUsername(), $extension->getSipNumber());
		}
	}
}

/* Catch possible errors */
catch (KerioApiException $error) {
	$html->printError($error->getMessage());
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

$html->printLegend();
$html->printFooter();

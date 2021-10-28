<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Show message queue.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Show message queue';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Want to know if everything just works and if there are no stuck messages in the message queue?');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Message queue */
	$query_params = array('query' => array('orderBy' => array(array('columnName' => 'nextTry', 'direction' => 'Asc'))));
	$queue = $api->sendRequest('Queue.get', $query_params);

	/* Print messages */
	if ($queue['totalItems'] > 0) {
		$html->printSuccess(sprintf('Message queue contains %d message(s).', $queue['totalItems']));
		print '<table class="withBorder">';
		print '  <thead>';
		print '    <th class="withBorder">From</th>';
		print '    <th class="withBorder">To</th>';
		print '    <th class="withBorder">Next try</th>';
		print '    <th class="withBorder">Size</th>';
		print '  </thead>';
		print '  <tbody>';
		foreach ($queue['list'] as $message) {
			printf('<tr><td class="withBorder">%s</td><td class="withBorder">%s</td><td class="withBorder">%s</td><td class="withBorder">%s %s</td></tr>',
				$message['from'], $message['to'], $message['nextTry'], $message['messageSize']['value'], $message['messageSize']['units']);
		}
		print '  </tbody>';
		print '</table>';
	}
	else {
		print '<i>Message queue is empty.</i>';
	}
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

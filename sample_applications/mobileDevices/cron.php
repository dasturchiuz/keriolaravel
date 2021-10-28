<?php
/**
 * Mobile Devices using ActiveSync - Sample Configuration.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/class/Html.php');
require_once(dirname(__FILE__) . '/class/Cron.php');
require_once(dirname(__FILE__) . '/class/MobileDevices.php');
require_once(dirname(__FILE__) . '/config/config.php');

set_time_limit(0); 

/* Print HTML */
$html = new Html();
$html->printHeader();
print '<h1 class="cron">Cron service</h1>';

/* Main application */
if(isset($_POST['runUpdate']) || isset($_GET['runUpdate'])) {

	/* Main application */
	$result = 'success';
	
	/* Start timer */
	list($msec1, $sec1) = explode(' ', microtime());

	/* Open temporarily DB connection*/
	$tmp = sprintf('%s.tmp.%d', $db_file, rand(1, 999));

	/* Cron update */
	try {
		$cron = new Cron($tmp);

		/* Loop servers */
		foreach ($servers as $server) {
		
			try {
				$cron->start($server);
				$cron->update();
				$cron->done();
				printf('<div class="success">Success: %s</div>', $server['hostname']); 
			}
			catch (Exception $error) {
				printf('<div class="error">Error: %s - %s</div>', $server['hostname'], $error->getMessage());
			}
			flush();

		}

		/* Stop timer */
		list($msec2, $sec2) = explode(" ", microtime());
		$duration = number_format((($sec2 + $msec2) - ($sec1 + $msec1)), 3);
		$cron->setTimestamp($duration);
	}
	catch (Exception $error) {
		/* Catch possible errors */
		printf('<div class="error">%s</div>', $error->getMessage());
		$result = 'error';
	}

	/* Set new DB and remove the temporarily one*/
	$command = ($result == 'success')
		? sprintf('mv %s %s', $tmp, $db_file)
		: sprintf('rm %s', $tmp);
	exec($command);
	
} //end of runUpdate

/**
 * Overview with last update info
 */

/* Open DB connection */
try {
	$application = new MobileDevices($db_file);

	$stats = $application->getLog();
	$html->printLastRunStatistics($stats);
	
	/* Run update form */
	$html->printRunUpdateForm();	
}
catch(Exception $error) {
	print $error->getMessage();
}

$html->printFooter();

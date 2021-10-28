<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/class/Server.php');

/* Database */
$db = 'db/database.db';
$Server = new Server($db);

/* Set runtime limit */
set_time_limit(0);

/* Print page header */
require_once(dirname(__FILE__) . '/include/header.php');

print '<h1 class="main">Cron service</h1>';

/* Main application */
try {
	/* Update loop */
	foreach ($Server->getServersFromDb() as $server) {

		try {
			$Server->update($server['id']);
			printf('<div class="success">Success: %s</div>', $server['hostname']);
		}
		catch (Exception $error) {
			printf('<div class="error">Error: %s - %s</div>', $server['hostname'], $error->getMessage());
		}
	}
}
/* Catch possible errors */
catch (Exception $error) {
	print $error->getMessage();
}

/* Update timestamp */
$Server->setDbTimestamp();

print '<a href="index.php">View data</a>';

/* Print page footer */
require_once(dirname(__FILE__) . '/include/footer.php');

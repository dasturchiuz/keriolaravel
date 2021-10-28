<?php
/**
 * Mobile Devices using ActiveSync - Sample Configuration.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/class/Html.php');
require_once(dirname(__FILE__) . '/class/MobileDevices.php');
require_once(dirname(__FILE__) . '/config/config.php');

/* Print HTML */
$html = new Html();
$html->printHeader();

/* Main application */
try {
	/* Open DB connection */
	$application = new MobileDevices($db_file);

	/* Get username from request */
	if(isset($_GET['u'])) {
		$username = $application->filterInput($_GET['u']);
	}
	
	/* Validate */
	if(empty($username)) {
		throw new Exception('Missing username.');
	}

	if(preg_match('/[^a-z0-9._-]/i', $username)) {
		throw new Exception('Wrong username.');
	}

	$userDetails = $application->getUser($username);
	if(empty($userDetails)) {
		throw new Exception('Invalid username.');
	}

	/* Print title */
	$fullname = ($userDetails['fullname']) ? $userDetails['fullname'] : $username;
	printf('<h1 class="user">%s\'s devices</h1>', $fullname);
	
	print '<table id="table-list" class="tablesorter">';
	print ' <thead>';
	print '    <tr>';
	print '		<th>Operating System</th>';
	print '		<th>Platform</th>';
	print '		<th>Protocol</th>';
	print '		<th class="headerSortUp">Last Synchronized</th>';
	print '	   </tr>';
	print ' </thead>';
	
	/* Get user's devices */
	$result = $application->getUserDevices($username);

	print ' <tbody>';
	foreach($result as $row)
	{
		print '<tr>';
		printf('<td>%s</td><td>%s</td><td>%s</td><td>%s</td>',
			$row['os'],
			$row['platform'],
			$row['protocolVersion'],
			date('Y-m-d H:i:s', $row['lastSyncDate']));
		print '</tr>';
	}
	print ' </tbody>';
	print '</table>';
}
catch (Exception $error) {
	print $error->getMessage();
}

$html->printFooter();

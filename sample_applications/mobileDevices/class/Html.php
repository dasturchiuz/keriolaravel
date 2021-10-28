<?php
/**
 * Mobile Devices using ActiveSync - Sample Application.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class Html {
	
	public function printHeader() {
		print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">'
			. '<html>'
			. '	<head>'
			. '		<meta http-equiv="Content-type" content="text/html; charset=UTF-8">'
			. '		<link rel="stylesheet" type="text/css" href="css/style.css">'
			. '		<link rel="stylesheet" type="text/css" href="css/tablesorter.css">'
			. '		<link rel="shortcut icon" href="../../doc/resources/images/favicon.ico">'
			. '		<script type="text/javascript" src="js/popup.js"></script>'
			. '		<script type="text/javascript" src="js/collapse.js"></script>'
			. '		<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>'
			. '		<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>'
			. '		<script type="text/javascript" src="js/jquery.tablelist.js"></script>'
			. '		<title>Mobile Devices using ActiveSync</title>'
			. '	</head>'
			. '<body>';
	}

	public function printFooter() {
		print '</body>'
			. '</html>';
	}

	public function printMenu() {
		print '<p>'
			. '<a href="' . $_SERVER['PHP_SELF'] . '">All results</a>'
			. ' | <a href="javascript:collapse(\'search\');">Search</a>'
			. ' | <a href="cron.php">Update</a>'
			. ' | <a href="javascript:collapse(\'debug\');">$_POST data</a>'
			. ' | <a href="javascript:collapse(\'sql\');">SQL</a>'
			. ' | <a href=javascript:openCenterWin("stats.php",757,738)>Statistics</a>'
			. '</p>';
	}

	public function printTimestamp($timestamp) {
		print '<hr>';
		printf('<i>Last update: %s</i> | Version 1.4.0.234', $timestamp);
	}

	public function printSelectWithOptions($name, $data) {
		$default = 'Any';
		printf('<select name="%s">', $name);
		printf('<option value="">%s</option>', $default);
		foreach($data as $row) {
			printf('<option>%s</options>', $row[$name]);
		}
		print '</select>';
	}
	
	public function printRunUpdateForm() {
		print '<form method="POST" action="">'
			. '<a href="index.php">View data</a> or ' 
			. '<input type="submit" name="runUpdate" value="Run update">'
			. '</form>';
	}
	
	public function printLastRunStatistics($data) {
		print '<div class="debug">'
			. 'Only users using Microsoft ActiveSync protocol with at least one device are displayed.<br>'
			. 'Please note that some devices, e.g. Android, use regular IMAP protocol.<br>'
			. 'In such case device is not logged nor displayed.<br>'
			. 'There is no limit of wiped or inactive devices.<br>'
			. 'Remember, running update might take a while.'
			. '</div>';
	
		foreach ($data as $log) {
			print '<p>';
			print '<table class="update">';
			print '	<tr>';
			print '		<th class="update">Last update</th>';
			printf('		<td class="update">%s</td>', $log['timestamp']);
			print '	</tr>';
			print '	<tr>';
			print '		<th class="update">Run time</th>';
			printf('		<td class="update">%d seconds	</td>', $log['duration']);
			print '	</tr>';
			print '	<tr>';
			print '		<th class="update">Processed users</th>';
			printf('		<td class="update">%d</td>', $log['userCount']);
			print '	</tr>';
			print '	<tr>';
			print '		<th class="update">Users with a device</th>';
			printf('		<td class="update">%d</td>', $log['userCountWithDevice']);
			print '	</tr>';
			print '	<tr>';
			print '		<th class="update">Devices</th>';
			printf('		<td class="update">%d</td>', $log['deviceCount']);
			print '	</tr>';
			print '</table>';
			print '</p>';	
		}
	}
}

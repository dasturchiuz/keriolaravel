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

$application = new MobileDevices($db_file);
$html = new Html();

/* Main application */
$html->printHeader();
print '<h1 class="main">Mobile Devices using ActiveSync</h1>';

/* Menu links */
$html->printMenu();

/* Search form */
print '<div id="search" style="display:none">';
print '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST">';
print '<table class="search">';
print '<tr>';
print '	<th class="search">Username</th>';
print '	<td class="search"><input type="text" name="username"></td>';
print '</tr>';
print '<tr>';
print '	<th class="search">Homeserver</th>';
print '	<td class="search">';
$data = $application->getFromTable('users', 'homeserver');
$html->printSelectWithOptions('homeserver', $data);
print '	</td>';
print '</tr>';
print '<tr>';
print '	<th class="search">Number of devices</th>';
print '	<td class="search">';
print '		<select name="devices">';
print '			<option value="">Any</option>';
print '			<option value="1">One</option>';
print '			<option value="2">Less than five</option>';
print '			<option value="3">More than five</option>';
print '		</select>';
print '	</td>';
print '</tr>';
print '<tr>';
print '	<th class="search">Operating system</th>';
print '	<td class="search">';
$data = $application->getFromTable('devices', 'os');
$html->printSelectWithOptions('os', $data);
print '	</td>';
print '</tr>';
print '<tr>';
print '	<th class="search">Platform</th>';
print '	<td class="search">';
$data = $application->getFromTable('devices', 'platform');
$html->printSelectWithOptions('platform', $data);
print '	</td>';
print '</tr>';
print '<tr>';
print '	<th class="search">Protocol version</th>';
print '	<td class="search">';
$data = $application->getFromTable('devices', 'protocolVersion');
$html->printSelectWithOptions('protocolVersion', $data);

print '	</td>';
print '</tr>';
print '<tr>';
print '	<th class="search">Last synchronized in</th>';
print '	<td class="search">';
print '		<select name="lastSyncDate">';
print '			<option value="">Any time</option>';
print '			<option value="today">Last 24 hours</option>';
print '			<option value="week">Last week</option>';
print '			<option value="month">Last month</option>';
print '			<option value="year">Last year</option>';
print '			<option value="yearago">Year ago</option>';
print '		</select>';
print '	</td>';
print '</tr>';
print '</table>';
print '<p><input type="submit" value="Search"></p>';
print '</form>';
print '</div>';

/* Print POST data */
print '<div class="debug" id="debug" style="display: none">';
$application->dumpPostData();
print '</div>';

/* Print SQL query */
$query = $application->buildQuery();
print '<div class="debug" id="sql" style="display: none">';
printf('<b>Query:</b> %s', $query);
print '</div>';

/* Print user list */
$result = $application->dbQuery($query);

if($result) {
	printf('<p>Found users: %d</p>', count($result));
	print '<table id="table-list" class="tablesorter">'
		. '	<thead>'
		. '	   <tr>'
		. '		<th class="headerSortDown">Username</th>'
		. '		<th>Fullname</th>'
		. '		<th>Devices</th>'
		. '		<th>Homeserver</th>'
		. '		<th>&nbsp;</th>'
		. '	   </tr>'
		. '	</thead>'
		. '	<tbody>';

	foreach($result as $row) {
		print '<tr>';
		printf("<td>%s</td><td>%s</td><td>%d</td><td>%s</td><td>
			<a href=javascript:openCenterWin(\"user.php?u=%s\",900,650)>
				Details
			</a>",
			$row['username'],
			$row['fullname'],
			$row['devices'],
			$row['homeserver'],
			strtolower($row['username']));
		print '</tr>';
	}
	print ' </tbody>';
	print '</table>';
}
else {
	print '<div class="error">Nothing to display.</div>';
}

$html->printTimestamp($application->getTimestamp());
$html->printFooter();

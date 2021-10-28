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
print '<h1 class="main">Mobile statistics by vendor</h1>';

/* Main application */
try {
	/* Open DB connection */
	$application = new MobileDevices('db/database.db');

	/* SQL query */
	$platforms = array();
	
	$query = 'SELECT d.os,d.username FROM devices d';
	$result = $application->dbQuery($query);
	
	foreach ($result as $phone) {
		$name = $phone['os'];
		(isset($platforms[$name]))
			? $platforms[$name]++
			: $platforms[$name] = 0;
	}
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);

	function drawChart() {
		var data = new google.visualization.DataTable();

		data.addColumn('string', 'Task');
		data.addColumn('number', 'Hours per Day');
		data.addRows(<?php print count($platforms); ?>);

		<?php
		$index = 0;
		foreach ($platforms as $phone => $count) {
			printf('data.setValue(%d, 0, "%s");', $index, $phone);
			printf('data.setValue(%d, 1, %d);', $index, $count);
			$index++;
		}
		?>
		var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
		
		chart.draw(data, {width: 720, height: 568});
	}
</script>

<div id="chart_div"></div>

<?php
}
catch (Exception $error) {
	print $error->getMessage();
}

$html->printFooter();
